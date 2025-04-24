import {Component} from './component';
import {findTargetNodeAndOffset, getLoading, hashElement, htmlToDom} from '../services/dom.ts';
import {buildForInput} from '../wysiwyg-tinymce/config';
import {el} from "../wysiwyg/utils/dom";

import commentIcon from "@icons/comment.svg"
import closeIcon from "@icons/close.svg"

/**
 * Track the close function for the current open marker so it can be closed
 * when another is opened so we only show one marker comment thread at one time.
 */
let openMarkerClose: Function|null = null;

export class PageComment extends Component {

    protected commentId: string;
    protected commentLocalId: string;
    protected commentContentRef: string;
    protected deletedText: string;
    protected updatedText: string;
    protected viewCommentText: string;
    protected jumpToThreadText: string;
    protected closeText: string;

    protected wysiwygEditor: any = null;
    protected wysiwygLanguage: string;
    protected wysiwygTextDirection: string;

    protected container: HTMLElement;
    protected contentContainer: HTMLElement;
    protected form: HTMLFormElement;
    protected formCancel: HTMLElement;
    protected editButton: HTMLElement;
    protected deleteButton: HTMLElement;
    protected replyButton: HTMLElement;
    protected input: HTMLInputElement;

    setup() {
        // Options
        this.commentId = this.$opts.commentId;
        this.commentLocalId = this.$opts.commentLocalId;
        this.commentContentRef = this.$opts.commentContentRef;
        this.deletedText = this.$opts.deletedText;
        this.updatedText = this.$opts.updatedText;
        this.viewCommentText = this.$opts.viewCommentText;
        this.jumpToThreadText = this.$opts.jumpToThreadText;
        this.closeText = this.$opts.closeText;

        // Editor reference and text options
        this.wysiwygLanguage = this.$opts.wysiwygLanguage;
        this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;

        // Element references
        this.container = this.$el;
        this.contentContainer = this.$refs.contentContainer;
        this.form = this.$refs.form as HTMLFormElement;
        this.formCancel = this.$refs.formCancel;
        this.editButton = this.$refs.editButton;
        this.deleteButton = this.$refs.deleteButton;
        this.replyButton = this.$refs.replyButton;
        this.input = this.$refs.input as HTMLInputElement;

        this.setupListeners();
        this.positionForReference();
    }

    protected setupListeners(): void {
        if (this.replyButton) {
            this.replyButton.addEventListener('click', () => this.$emit('reply', {
                id: this.commentLocalId,
                element: this.container,
            }));
        }

        if (this.editButton) {
            this.editButton.addEventListener('click', this.startEdit.bind(this));
            this.form.addEventListener('submit', this.update.bind(this));
            this.formCancel.addEventListener('click', () => this.toggleEditMode(false));
        }

        if (this.deleteButton) {
            this.deleteButton.addEventListener('click', this.delete.bind(this));
        }
    }

    protected toggleEditMode(show: boolean) : void {
        this.contentContainer.toggleAttribute('hidden', show);
        this.form.toggleAttribute('hidden', !show);
    }

    protected startEdit() : void {
        this.toggleEditMode(true);

        if (this.wysiwygEditor) {
            this.wysiwygEditor.focus();
            return;
        }

        const config = buildForInput({
            language: this.wysiwygLanguage,
            containerElement: this.input,
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.wysiwygTextDirection,
            drawioUrl: '',
            pageId: 0,
            translations: {},
            translationMap: (window as Record<string, Object>).editor_translations,
        });

        (window as {tinymce: {init: (Object) => Promise<any>}}).tinymce.init(config).then(editors => {
            this.wysiwygEditor = editors[0];
            setTimeout(() => this.wysiwygEditor.focus(), 50);
        });
    }

    protected async update(event: Event): Promise<void> {
        event.preventDefault();
        const loading = this.showLoading();
        this.form.toggleAttribute('hidden', true);

        const reqData = {
            html: this.wysiwygEditor.getContent(),
        };

        try {
            const resp = await window.$http.put(`/comment/${this.commentId}`, reqData);
            const newComment = htmlToDom(resp.data as string);
            this.container.replaceWith(newComment);
            window.$events.success(this.updatedText);
        } catch (err) {
            console.error(err);
            window.$events.showValidationErrors(err);
            this.form.toggleAttribute('hidden', false);
            loading.remove();
        }
    }

    protected async delete(): Promise<void> {
        this.showLoading();

        await window.$http.delete(`/comment/${this.commentId}`);
        this.$emit('delete');
        this.container.closest('.comment-branch')?.remove();
        window.$events.success(this.deletedText);
    }

    protected showLoading(): HTMLElement {
        const loading = getLoading();
        loading.classList.add('px-l');
        this.container.append(loading);
        return loading;
    }

    protected positionForReference() {
        if (!this.commentContentRef) {
            return;
        }

        const [refId, refHash, refRange] = this.commentContentRef.split(':');
        const refEl = document.getElementById(refId);
        if (!refEl) {
            // TODO - Show outdated marker for comment
            return;
        }

        const actualHash = hashElement(refEl);
        if (actualHash !== refHash) {
            // TODO - Show outdated marker for comment
            return;
        }

        const refElBounds = refEl.getBoundingClientRect();
        let bounds = refElBounds;
        const [rangeStart, rangeEnd] = refRange.split('-');
        if (rangeStart && rangeEnd) {
            const range = new Range();
            const relStart = findTargetNodeAndOffset(refEl, Number(rangeStart));
            const relEnd = findTargetNodeAndOffset(refEl, Number(rangeEnd));
            if (relStart && relEnd) {
                range.setStart(relStart.node, relStart.offset);
                range.setEnd(relEnd.node, relEnd.offset);
                bounds = range.getBoundingClientRect();
            }
        }

        const relLeft = bounds.left - refElBounds.left;
        const relTop = bounds.top - refElBounds.top;

        const marker = el('button', {
            type: 'button',
            class: 'content-comment-marker',
            title: this.viewCommentText,
        });
        marker.innerHTML = <string>commentIcon;
        marker.addEventListener('click', event => {
            this.showCommentAtMarker(marker);
        });

        const markerWrap = el('div', {
            class: 'content-comment-highlight',
            style: `left: ${relLeft}px; top: ${relTop}px; width: ${bounds.width}px; height: ${bounds.height}px;`
        }, [marker]);

        refEl.style.position = 'relative';
        refEl.append(markerWrap);
    }

    protected showCommentAtMarker(marker: HTMLElement): void {
        // Hide marker and close existing marker windows
        if (openMarkerClose) {
            openMarkerClose();
        }
        marker.hidden = true;

        // Build comment window
        const readClone = (this.container.closest('.comment-branch') as HTMLElement).cloneNode(true) as HTMLElement;
        const toRemove = readClone.querySelectorAll('.actions, form');
        for (const el of toRemove) {
            el.remove();
        }

        const close = el('button', {type: 'button', title: this.closeText});
        close.innerHTML = (closeIcon as string);
        const jump = el('button', {type: 'button', 'data-action': 'jump'}, [this.jumpToThreadText]);

        const commentWindow = el('div', {
            class: 'content-comment-window'
        }, [
            el('div', {
                class: 'content-comment-window-actions',
            }, [jump, close]),
            el('div', {
                class: 'content-comment-window-content comment-container-compact comment-container-super-compact',
            }, [readClone]),
        ]);

        marker.parentElement?.append(commentWindow);

        // Handle interaction within window
        const closeAction = () => {
            commentWindow.remove();
            marker.hidden = false;
            window.removeEventListener('click', windowCloseAction);
            openMarkerClose = null;
        };

        const windowCloseAction = (event: MouseEvent) => {
            if (!(marker.parentElement as HTMLElement).contains(event.target as HTMLElement)) {
                closeAction();
            }
        };
        window.addEventListener('click', windowCloseAction);

        openMarkerClose = closeAction;
        close.addEventListener('click', closeAction.bind(this));
        jump.addEventListener('click', () => {
            closeAction();
            this.container.scrollIntoView({behavior: 'smooth'});
            const highlightTarget = this.container.querySelector('.header') as HTMLElement;
            highlightTarget.classList.add('anim-highlight');
            highlightTarget.addEventListener('animationend', () => highlightTarget.classList.remove('anim-highlight'))
        });

        // Position window within bounds
        const commentWindowBounds = commentWindow.getBoundingClientRect();
        const contentBounds = document.querySelector('.page-content')?.getBoundingClientRect();
        if (contentBounds && commentWindowBounds.right > contentBounds.right) {
            const diff = commentWindowBounds.right - contentBounds.right;
            commentWindow.style.left = `-${diff}px`;
        }
    }
}

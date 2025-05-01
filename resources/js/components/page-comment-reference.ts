import {Component} from "./component";
import {findTargetNodeAndOffset, hashElement} from "../services/dom";
import {el} from "../wysiwyg/utils/dom";
import commentIcon from "@icons/comment.svg";
import closeIcon from "@icons/close.svg";
import {debounce, scrollAndHighlightElement} from "../services/util";

/**
 * Track the close function for the current open marker so it can be closed
 * when another is opened so we only show one marker comment thread at one time.
 */
let openMarkerClose: Function|null = null;

export class PageCommentReference extends Component {
    protected link: HTMLLinkElement;
    protected reference: string;
    protected markerWrap: HTMLElement|null = null;

    protected viewCommentText: string;
    protected jumpToThreadText: string;
    protected closeText: string;

    setup() {
        this.link = this.$el as HTMLLinkElement;
        this.reference = this.$opts.reference;
        this.viewCommentText = this.$opts.viewCommentText;
        this.jumpToThreadText = this.$opts.jumpToThreadText;
        this.closeText = this.$opts.closeText;

        // Show within page display area if seen
        const pageContentArea = document.querySelector('.page-content');
        if (pageContentArea instanceof HTMLElement && this.link.checkVisibility()) {
            this.updateMarker(pageContentArea);
        }

        // Handle editor view to show on comments toolbox view
        window.addEventListener('editor-toolbox-change', (event) => {
             const tabName: string = (event as {detail: {tab: string, open: boolean}}).detail.tab;
             const isOpen = (event as {detail: {tab: string, open: boolean}}).detail.open;
             if (tabName === 'comments' && isOpen) {
                 this.showForEditor();
             } else {
                 this.hideMarker();
             }
        });

        // Handle comments tab changes to hide/show markers & indicators
        window.addEventListener('tabs-change', event => {
            const sectionId = (event as {detail: {showing: string}}).detail.showing;
            if (!sectionId.startsWith('comment-tab-panel') || !(pageContentArea instanceof HTMLElement)) {
                return;
            }

            const panel = document.getElementById(sectionId);
            if (panel?.contains(this.link)) {
                this.updateMarker(pageContentArea);
            } else {
                this.hideMarker();
            }
        });
    }

    protected showForEditor() {
        const contentWrap = document.querySelector('.editor-content-wrap');
        if (contentWrap instanceof HTMLElement) {
            this.updateMarker(contentWrap);
        }

        const onChange = () => {
            this.hideMarker();
            setTimeout(() => {
                window.$events.remove('editor-html-change', onChange);
            }, 1);
        };

        window.$events.listen('editor-html-change', onChange);
    }

    protected updateMarker(contentContainer: HTMLElement) {
        // Reset link and existing marker
        this.link.classList.remove('outdated', 'missing');
        if (this.markerWrap) {
            this.markerWrap.remove();
        }

        const [refId, refHash, refRange] = this.reference.split(':');
        const refEl = document.getElementById(refId);
        if (!refEl) {
            this.link.classList.add('outdated', 'missing');
            return;
        }

        const refCloneToAssess = refEl.cloneNode(true) as HTMLElement;
        const toRemove = refCloneToAssess.querySelectorAll('[data-lexical-text]');
        refCloneToAssess.removeAttribute('style');
        for (const el of toRemove) {
            el.after(...el.childNodes);
            el.remove();
        }

        const actualHash = hashElement(refCloneToAssess);
        if (actualHash !== refHash) {
            this.link.classList.add('outdated');
        }

        const marker = el('button', {
            type: 'button',
            class: 'content-comment-marker',
            title: this.viewCommentText,
        });
        marker.innerHTML = <string>commentIcon;
        marker.addEventListener('click', event => {
            this.showCommentAtMarker(marker);
        });

        this.markerWrap = el('div', {
            class: 'content-comment-highlight',
        }, [marker]);

        contentContainer.append(this.markerWrap);
        this.positionMarker(refEl, refRange);

        this.link.href = `#${refEl.id}`;
        this.link.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            scrollAndHighlightElement(refEl);
        });

        const debouncedReposition = debounce(() => {
            this.positionMarker(refEl, refRange);
        }, 50, false).bind(this);
        window.addEventListener('resize', debouncedReposition);
    }

    protected positionMarker(targetEl: HTMLElement, range: string) {
        if (!this.markerWrap) {
            return;
        }

        const markerParent = this.markerWrap.parentElement as HTMLElement;
        const parentBounds = markerParent.getBoundingClientRect();
        let targetBounds = targetEl.getBoundingClientRect();
        const [rangeStart, rangeEnd] = range.split('-');
        if (rangeStart && rangeEnd) {
            const range = new Range();
            const relStart = findTargetNodeAndOffset(targetEl, Number(rangeStart));
            const relEnd = findTargetNodeAndOffset(targetEl, Number(rangeEnd));
            if (relStart && relEnd) {
                range.setStart(relStart.node, relStart.offset);
                range.setEnd(relEnd.node, relEnd.offset);
                targetBounds = range.getBoundingClientRect();
            }
        }

        const relLeft = targetBounds.left - parentBounds.left;
        const relTop = (targetBounds.top - parentBounds.top) + markerParent.scrollTop;

        this.markerWrap.style.left = `${relLeft}px`;
        this.markerWrap.style.top = `${relTop}px`;
        this.markerWrap.style.width = `${targetBounds.width}px`;
        this.markerWrap.style.height = `${targetBounds.height}px`;
    }

    public hideMarker() {
        // Hide marker and close existing marker windows
        if (openMarkerClose) {
            openMarkerClose();
        }
        this.markerWrap?.remove();
        this.markerWrap = null;
    }

    protected showCommentAtMarker(marker: HTMLElement): void {
        // Hide marker and close existing marker windows
        if (openMarkerClose) {
            openMarkerClose();
        }
        marker.hidden = true;

        // Locate relevant comment
        const commentBox = this.link.closest('.comment-box') as HTMLElement;

        // Build comment window
        const readClone = (commentBox.closest('.comment-branch') as HTMLElement).cloneNode(true) as HTMLElement;
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
            commentBox.scrollIntoView({behavior: 'smooth'});
            const highlightTarget = commentBox.querySelector('.header') as HTMLElement;
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
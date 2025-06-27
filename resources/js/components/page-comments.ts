import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom';
import {Tabs} from "./tabs";
import {PageCommentReference} from "./page-comment-reference";
import {scrollAndHighlightElement} from "../services/util";
import {PageCommentArchiveEventData, PageCommentReplyEventData} from "./page-comment";
import {el} from "../wysiwyg/utils/dom";
import {SimpleWysiwygEditorInterface} from "../wysiwyg";

export class PageComments extends Component {

    private elem!: HTMLElement;
    private pageId!: number;
    private container!: HTMLElement;
    private commentCountBar!: HTMLElement;
    private activeTab!: HTMLElement;
    private archivedTab!: HTMLElement;
    private addButtonContainer!: HTMLElement;
    private archiveContainer!: HTMLElement;
    private activeContainer!: HTMLElement;
    private replyToRow!: HTMLElement;
    private referenceRow!: HTMLElement;
    private formContainer!: HTMLElement;
    private form!: HTMLFormElement;
    private formInput!: HTMLInputElement;
    private formReplyLink!: HTMLAnchorElement;
    private formReferenceLink!: HTMLAnchorElement;
    private addCommentButton!: HTMLElement;
    private hideFormButton!: HTMLElement;
    private removeReplyToButton!: HTMLElement;
    private removeReferenceButton!: HTMLElement;
    private wysiwygTextDirection!: string;
    private wysiwygEditor: SimpleWysiwygEditorInterface|null = null;
    private createdText!: string;
    private countText!: string;
    private archivedCountText!: string;
    private parentId: number | null = null;
    private contentReference: string = '';
    private formReplyText: string = '';

    setup() {
        this.elem = this.$el;
        this.pageId = Number(this.$opts.pageId);

        // Element references
        this.container = this.$refs.commentContainer;
        this.commentCountBar = this.$refs.commentCountBar;
        this.activeTab = this.$refs.activeTab;
        this.archivedTab = this.$refs.archivedTab;
        this.addButtonContainer = this.$refs.addButtonContainer;
        this.archiveContainer = this.$refs.archiveContainer;
        this.activeContainer = this.$refs.activeContainer;
        this.replyToRow = this.$refs.replyToRow;
        this.referenceRow = this.$refs.referenceRow;
        this.formContainer = this.$refs.formContainer;
        this.form = this.$refs.form as HTMLFormElement;
        this.formInput = this.$refs.formInput as HTMLInputElement;
        this.formReplyLink = this.$refs.formReplyLink as HTMLAnchorElement;
        this.formReferenceLink = this.$refs.formReferenceLink as HTMLAnchorElement;
        this.addCommentButton = this.$refs.addCommentButton;
        this.hideFormButton = this.$refs.hideFormButton;
        this.removeReplyToButton = this.$refs.removeReplyToButton;
        this.removeReferenceButton = this.$refs.removeReferenceButton;

        // WYSIWYG options
        this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;

        // Translations
        this.createdText = this.$opts.createdText;
        this.countText = this.$opts.countText;
        this.archivedCountText = this.$opts.archivedCountText;

        this.formReplyText = this.formReplyLink?.textContent || '';

        this.setupListeners();
    }

    protected setupListeners(): void {
        this.elem.addEventListener('page-comment-delete', () => {
            setTimeout(() => {
                this.updateCount();
                this.hideForm();
            }, 1);
        });

        this.elem.addEventListener('page-comment-reply', ((event: CustomEvent<PageCommentReplyEventData>) => {
            this.setReply(event.detail.id, event.detail.element);
        }) as EventListener);

        this.elem.addEventListener('page-comment-archive', ((event: CustomEvent<PageCommentArchiveEventData>) => {
            this.archiveContainer.append(event.detail.new_thread_dom);
            setTimeout(() => this.updateCount(), 1);
        }) as EventListener);

        this.elem.addEventListener('page-comment-unarchive', ((event: CustomEvent<PageCommentArchiveEventData>) => {
            this.container.append(event.detail.new_thread_dom);
            setTimeout(() => this.updateCount(), 1);
        }) as EventListener);

        if (this.form) {
            this.removeReplyToButton.addEventListener('click', this.removeReplyTo.bind(this));
            this.removeReferenceButton.addEventListener('click', () => this.setContentReference(''));
            this.hideFormButton.addEventListener('click', this.hideForm.bind(this));
            this.addCommentButton.addEventListener('click', this.showForm.bind(this));
            this.form.addEventListener('submit', this.saveComment.bind(this));
        }
    }

    protected async saveComment(event: SubmitEvent): Promise<void> {
        event.preventDefault();
        event.stopPropagation();

        const loading = getLoading();
        loading.classList.add('px-l');
        this.form.after(loading);
        this.form.toggleAttribute('hidden', true);

        const reqData = {
            html: (await this.wysiwygEditor?.getContentAsHtml()) || '',
            parent_id: this.parentId || null,
            content_ref: this.contentReference,
        };

        window.$http.post(`/comment/${this.pageId}`, reqData).then(resp => {
            const newElem = htmlToDom(resp.data as string);

            if (reqData.parent_id) {
                this.formContainer.after(newElem);
            } else {
                this.container.append(newElem);
            }

            const refs = window.$components.allWithinElement<PageCommentReference>(newElem, 'page-comment-reference');
            for (const ref of refs) {
                ref.showForDisplay();
            }

            window.$events.success(this.createdText);
            this.hideForm();
            this.updateCount();
        }).catch(err => {
            this.form.toggleAttribute('hidden', false);
            window.$events.showValidationErrors(err);
        });

        this.form.toggleAttribute('hidden', false);
        loading.remove();
    }

    protected updateCount(): void {
        const activeCount = this.getActiveThreadCount();
        this.activeTab.textContent = window.$trans.choice(this.countText, activeCount);
        const archivedCount = this.getArchivedThreadCount();
        this.archivedTab.textContent = window.$trans.choice(this.archivedCountText, archivedCount);
    }

    protected resetForm(): void {
        this.removeEditor();
        this.formInput.value = '';
        this.parentId = null;
        this.replyToRow.toggleAttribute('hidden', true);
        this.container.append(this.formContainer);
        this.setContentReference('');
    }

    protected showForm(): void {
        this.removeEditor();
        this.formContainer.toggleAttribute('hidden', false);
        this.addButtonContainer.toggleAttribute('hidden', true);
        this.formContainer.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        this.loadEditor();

        // Ensure the active comments tab is displaying if that's where we're showing the form
        const tabs = window.$components.firstOnElement(this.elem, 'tabs');
        if (tabs instanceof Tabs && this.formContainer.closest('#comment-tab-panel-active')) {
            tabs.show('comment-tab-panel-active');
        }
    }

    protected hideForm(): void {
        this.resetForm();
        this.formContainer.toggleAttribute('hidden', true);
        if (this.getActiveThreadCount() > 0) {
            this.activeContainer.append(this.addButtonContainer);
        } else {
            this.commentCountBar.append(this.addButtonContainer);
        }
        this.addButtonContainer.toggleAttribute('hidden', false);
    }

    protected async loadEditor(): Promise<void> {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.focus();
            return;
        }

        type WysiwygModule = typeof import('../wysiwyg');
        const wysiwygModule = (await window.importVersioned('wysiwyg')) as WysiwygModule;
        const container = el('div', {class: 'comment-editor-container'});
        this.formInput.parentElement?.appendChild(container);
        this.formInput.hidden = true;

        this.wysiwygEditor = wysiwygModule.createBasicEditorInstance(container as HTMLElement, '<p></p>', {
            darkMode: document.documentElement.classList.contains('dark-mode'),
            textDirection: this.wysiwygTextDirection,
            translations: (window as unknown as Record<string, Object>).editor_translations,
        });

        this.wysiwygEditor.focus();
    }

    protected removeEditor(): void {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.remove();
            this.wysiwygEditor = null;
        }
    }

    protected getActiveThreadCount(): number {
        return this.container.querySelectorAll(':scope > .comment-branch:not([hidden])').length;
    }

    protected getArchivedThreadCount(): number {
        return this.archiveContainer.querySelectorAll(':scope > .comment-branch').length;
    }

    protected setReply(commentLocalId: string, commentElement: HTMLElement): void {
        const targetFormLocation = (commentElement.closest('.comment-branch') as HTMLElement).querySelector('.comment-branch-children') as HTMLElement;
        targetFormLocation.append(this.formContainer);
        this.showForm();
        this.parentId = Number(commentLocalId);
        this.replyToRow.toggleAttribute('hidden', false);
        this.formReplyLink.textContent = this.formReplyText.replace('1234', String(this.parentId));
        this.formReplyLink.href = `#comment${this.parentId}`;
    }

    protected removeReplyTo(): void {
        this.parentId = null;
        this.replyToRow.toggleAttribute('hidden', true);
        this.container.append(this.formContainer);
        this.showForm();
    }

    public startNewComment(contentReference: string): void {
        this.resetForm();
        this.showForm();
        this.setContentReference(contentReference);
    }

    protected setContentReference(reference: string): void {
        this.contentReference = reference;
        this.referenceRow.toggleAttribute('hidden', !Boolean(reference));
        const [id] = reference.split(':');
        this.formReferenceLink.href = `#${id}`;
        this.formReferenceLink.onclick = function(event) {
            event.preventDefault();
            const el = document.getElementById(id);
            if (el) {
                scrollAndHighlightElement(el);
            }
        };
    }

}

import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom.ts';
import {buildForInput} from '../wysiwyg-tinymce/config';

export interface CommentReplyEvent extends Event {
    detail: {
        id: string; // ID of comment being replied to
        element: HTMLElement; // Container for comment replied to
    }
}

export class PageComments extends Component {

    private elem: HTMLElement;
    private pageId: number;
    private container: HTMLElement;
    private commentCountBar: HTMLElement;
    private commentsTitle: HTMLElement;
    private addButtonContainer: HTMLElement;
    private replyToRow: HTMLElement;
    private formContainer: HTMLElement;
    private form: HTMLFormElement;
    private formInput: HTMLInputElement;
    private formReplyLink: HTMLAnchorElement;
    private addCommentButton: HTMLElement;
    private hideFormButton: HTMLElement;
    private removeReplyToButton: HTMLElement;
    private wysiwygLanguage: string;
    private wysiwygTextDirection: string;
    private wysiwygEditor: any = null;
    private createdText: string;
    private countText: string;
    private parentId: number | null = null;
    private contentReference: string = '';
    private formReplyText: string = '';

    setup() {
        this.elem = this.$el;
        this.pageId = Number(this.$opts.pageId);

        // Element references
        this.container = this.$refs.commentContainer;
        this.commentCountBar = this.$refs.commentCountBar;
        this.commentsTitle = this.$refs.commentsTitle;
        this.addButtonContainer = this.$refs.addButtonContainer;
        this.replyToRow = this.$refs.replyToRow;
        this.formContainer = this.$refs.formContainer;
        this.form = this.$refs.form as HTMLFormElement;
        this.formInput = this.$refs.formInput as HTMLInputElement;
        this.formReplyLink = this.$refs.formReplyLink as HTMLAnchorElement;
        this.addCommentButton = this.$refs.addCommentButton;
        this.hideFormButton = this.$refs.hideFormButton;
        this.removeReplyToButton = this.$refs.removeReplyToButton;

        // WYSIWYG options
        this.wysiwygLanguage = this.$opts.wysiwygLanguage;
        this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;

        // Translations
        this.createdText = this.$opts.createdText;
        this.countText = this.$opts.countText;

        this.formReplyText = this.formReplyLink?.textContent || '';

        this.setupListeners();
    }

    protected setupListeners(): void {
        this.elem.addEventListener('page-comment-delete', () => {
            setTimeout(() => this.updateCount(), 1);
            this.hideForm();
        });

        this.elem.addEventListener('page-comment-reply', (event: CommentReplyEvent) => {
            this.setReply(event.detail.id, event.detail.element);
        });

        if (this.form) {
            this.removeReplyToButton.addEventListener('click', this.removeReplyTo.bind(this));
            this.hideFormButton.addEventListener('click', this.hideForm.bind(this));
            this.addCommentButton.addEventListener('click', this.showForm.bind(this));
            this.form.addEventListener('submit', this.saveComment.bind(this));
        }
    }

    protected saveComment(event): void {
        event.preventDefault();
        event.stopPropagation();

        const loading = getLoading();
        loading.classList.add('px-l');
        this.form.after(loading);
        this.form.toggleAttribute('hidden', true);

        const reqData = {
            html: this.wysiwygEditor.getContent(),
            parent_id: this.parentId || null,
            content_ref: this.contentReference || '',
        };

        window.$http.post(`/comment/${this.pageId}`, reqData).then(resp => {
            const newElem = htmlToDom(resp.data as string);

            if (reqData.parent_id) {
                this.formContainer.after(newElem);
            } else {
                this.container.append(newElem);
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
        const count = this.getCommentCount();
        this.commentsTitle.textContent = window.$trans.choice(this.countText, count);
    }

    protected resetForm(): void {
        this.removeEditor();
        this.formInput.value = '';
        this.parentId = null;
        this.contentReference = '';
        this.replyToRow.toggleAttribute('hidden', true);
        this.container.append(this.formContainer);
    }

    protected showForm(): void {
        this.removeEditor();
        this.formContainer.toggleAttribute('hidden', false);
        this.addButtonContainer.toggleAttribute('hidden', true);
        this.formContainer.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        this.loadEditor();
    }

    protected hideForm(): void {
        this.resetForm();
        this.formContainer.toggleAttribute('hidden', true);
        if (this.getCommentCount() > 0) {
            this.elem.append(this.addButtonContainer);
        } else {
            this.commentCountBar.append(this.addButtonContainer);
        }
        this.addButtonContainer.toggleAttribute('hidden', false);
    }

    protected loadEditor(): void {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.focus();
            return;
        }

        const config = buildForInput({
            language: this.wysiwygLanguage,
            containerElement: this.formInput,
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

    protected removeEditor(): void {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.remove();
            this.wysiwygEditor = null;
        }
    }

    protected getCommentCount(): number {
        return this.container.querySelectorAll('[component="page-comment"]').length;
    }

    protected setReply(commentLocalId, commentElement): void {
        const targetFormLocation = commentElement.closest('.comment-branch').querySelector('.comment-branch-children');
        targetFormLocation.append(this.formContainer);
        this.showForm();
        this.parentId = commentLocalId;
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
        this.removeReplyTo();
        this.contentReference = contentReference;
    }

}

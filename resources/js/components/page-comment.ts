import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom.ts';
import {buildForInput} from '../wysiwyg-tinymce/config';
import {PageCommentReference} from "./page-comment-reference";

export class PageComment extends Component {

    protected commentId: string;
    protected commentLocalId: string;
    protected deletedText: string;
    protected updatedText: string;
    protected archiveText: string;

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
    protected archiveButton: HTMLElement;
    protected input: HTMLInputElement;

    setup() {
        // Options
        this.commentId = this.$opts.commentId;
        this.commentLocalId = this.$opts.commentLocalId;
        this.deletedText = this.$opts.deletedText;
        this.deletedText = this.$opts.deletedText;
        this.archiveText = this.$opts.archiveText;

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
        this.archiveButton = this.$refs.archiveButton;
        this.input = this.$refs.input as HTMLInputElement;

        this.setupListeners();
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

        if (this.archiveButton) {
            this.archiveButton.addEventListener('click', this.archive.bind(this));
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

        const branch = this.container.closest('.comment-branch');
        if (branch instanceof HTMLElement) {
            const refs = window.$components.allWithinElement<PageCommentReference>(branch, 'page-comment-reference');
            for (const ref of refs) {
                ref.hideMarker();
            }
            branch.remove();
        }

        window.$events.success(this.deletedText);
    }

    protected async archive(): Promise<void> {
        this.showLoading();
        const isArchived = this.archiveButton.dataset.isArchived === 'true';
        const action = isArchived ? 'unarchive' : 'archive';

        const response = await window.$http.put(`/comment/${this.commentId}/${action}`);
        window.$events.success(this.archiveText);
        this.$emit(action, {new_thread_dom: htmlToDom(response.data as string)});

        const branch = this.container.closest('.comment-branch') as HTMLElement;
        const references = window.$components.allWithinElement<PageCommentReference>(branch, 'page-comment-reference');
        for (const reference of references) {
            reference.hideMarker();
        }
        branch.remove();
    }

    protected showLoading(): HTMLElement {
        const loading = getLoading();
        loading.classList.add('px-l');
        this.container.append(loading);
        return loading;
    }
}

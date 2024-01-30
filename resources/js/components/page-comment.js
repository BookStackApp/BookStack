import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom';
import {buildForInput} from '../wysiwyg/config';

export class PageComment extends Component {

    setup() {
        // Options
        this.commentId = this.$opts.commentId;
        this.commentLocalId = this.$opts.commentLocalId;
        this.commentParentId = this.$opts.commentParentId;
        this.deletedText = this.$opts.deletedText;
        this.updatedText = this.$opts.updatedText;

        // Editor reference and text options
        this.wysiwygEditor = null;
        this.wysiwygLanguage = this.$opts.wysiwygLanguage;
        this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;

        // Element references
        this.container = this.$el;
        this.contentContainer = this.$refs.contentContainer;
        this.form = this.$refs.form;
        this.formCancel = this.$refs.formCancel;
        this.editButton = this.$refs.editButton;
        this.deleteButton = this.$refs.deleteButton;
        this.replyButton = this.$refs.replyButton;
        this.input = this.$refs.input;

        this.setupListeners();
    }

    setupListeners() {
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

    toggleEditMode(show) {
        this.contentContainer.toggleAttribute('hidden', show);
        this.form.toggleAttribute('hidden', !show);
    }

    startEdit() {
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
            translations: {},
            translationMap: window.editor_translations,
        });

        window.tinymce.init(config).then(editors => {
            this.wysiwygEditor = editors[0];
            setTimeout(() => this.wysiwygEditor.focus(), 50);
        });
    }

    async update(event) {
        event.preventDefault();
        const loading = this.showLoading();
        this.form.toggleAttribute('hidden', true);

        const reqData = {
            html: this.wysiwygEditor.getContent(),
            parent_id: this.parentId || null,
        };

        try {
            const resp = await window.$http.put(`/comment/${this.commentId}`, reqData);
            const newComment = htmlToDom(resp.data);
            this.container.replaceWith(newComment);
            window.$events.success(this.updatedText);
        } catch (err) {
            console.error(err);
            window.$events.showValidationErrors(err);
            this.form.toggleAttribute('hidden', false);
            loading.remove();
        }
    }

    async delete() {
        this.showLoading();

        await window.$http.delete(`/comment/${this.commentId}`);
        this.container.closest('.comment-branch').remove();
        window.$events.success(this.deletedText);
        this.$emit('delete');
    }

    showLoading() {
        const loading = getLoading();
        loading.classList.add('px-l');
        this.container.append(loading);
        return loading;
    }

}

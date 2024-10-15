import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom.ts';
import {buildForInput} from '../wysiwyg-tinymce/config';

export class PageComments extends Component {

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
        this.form = this.$refs.form;
        this.formInput = this.$refs.formInput;
        this.formReplyLink = this.$refs.formReplyLink;
        this.addCommentButton = this.$refs.addCommentButton;
        this.hideFormButton = this.$refs.hideFormButton;
        this.removeReplyToButton = this.$refs.removeReplyToButton;

        // WYSIWYG options
        this.wysiwygLanguage = this.$opts.wysiwygLanguage;
        this.wysiwygTextDirection = this.$opts.wysiwygTextDirection;
        this.wysiwygEditor = null;

        // Translations
        this.createdText = this.$opts.createdText;
        this.countText = this.$opts.countText;

        // Internal State
        this.parentId = null;
        this.formReplyText = this.formReplyLink?.textContent || '';

        this.setupListeners();
    }

    setupListeners() {
        this.elem.addEventListener('page-comment-delete', () => {
            setTimeout(() => this.updateCount(), 1);
            this.hideForm();
        });

        this.elem.addEventListener('page-comment-reply', event => {
            this.setReply(event.detail.id, event.detail.element);
        });

        if (this.form) {
            this.removeReplyToButton.addEventListener('click', this.removeReplyTo.bind(this));
            this.hideFormButton.addEventListener('click', this.hideForm.bind(this));
            this.addCommentButton.addEventListener('click', this.showForm.bind(this));
            this.form.addEventListener('submit', this.saveComment.bind(this));
        }
    }

    saveComment(event) {
        event.preventDefault();
        event.stopPropagation();

        const loading = getLoading();
        loading.classList.add('px-l');
        this.form.after(loading);
        this.form.toggleAttribute('hidden', true);

        const reqData = {
            html: this.wysiwygEditor.getContent(),
            parent_id: this.parentId || null,
        };

        window.$http.post(`/comment/${this.pageId}`, reqData).then(resp => {
            const newElem = htmlToDom(resp.data);

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

    updateCount() {
        const count = this.getCommentCount();
        console.log('update count', count, this.container);
        this.commentsTitle.textContent = window.$trans.choice(this.countText, count, {count});
    }

    resetForm() {
        this.removeEditor();
        this.formInput.value = '';
        this.parentId = null;
        this.replyToRow.toggleAttribute('hidden', true);
        this.container.append(this.formContainer);
    }

    showForm() {
        this.removeEditor();
        this.formContainer.toggleAttribute('hidden', false);
        this.addButtonContainer.toggleAttribute('hidden', true);
        this.formContainer.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        this.loadEditor();
    }

    hideForm() {
        this.resetForm();
        this.formContainer.toggleAttribute('hidden', true);
        if (this.getCommentCount() > 0) {
            this.elem.append(this.addButtonContainer);
        } else {
            this.commentCountBar.append(this.addButtonContainer);
        }
        this.addButtonContainer.toggleAttribute('hidden', false);
    }

    loadEditor() {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.focus();
            return;
        }

        const config = buildForInput({
            language: this.wysiwygLanguage,
            containerElement: this.formInput,
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

    removeEditor() {
        if (this.wysiwygEditor) {
            this.wysiwygEditor.remove();
            this.wysiwygEditor = null;
        }
    }

    getCommentCount() {
        return this.container.querySelectorAll('[component="page-comment"]').length;
    }

    setReply(commentLocalId, commentElement) {
        const targetFormLocation = commentElement.closest('.comment-branch').querySelector('.comment-branch-children');
        targetFormLocation.append(this.formContainer);
        this.showForm();
        this.parentId = commentLocalId;
        this.replyToRow.toggleAttribute('hidden', false);
        this.formReplyLink.textContent = this.formReplyText.replace('1234', this.parentId);
        this.formReplyLink.href = `#comment${this.parentId}`;
    }

    removeReplyTo() {
        this.parentId = null;
        this.replyToRow.toggleAttribute('hidden', true);
        this.container.append(this.formContainer);
        this.showForm();
    }

}

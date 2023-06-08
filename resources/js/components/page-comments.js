import {Component} from './component';
import {getLoading, htmlToDom} from '../services/dom';

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
        this.addCommentButton = this.$refs.addCommentButton;
        this.hideFormButton = this.$refs.hideFormButton;
        this.removeReplyToButton = this.$refs.removeReplyToButton;

        // Translations
        this.createdText = this.$opts.createdText;
        this.countText = this.$opts.countText;

        // Internal State
        this.parentId = null;

        this.setupListeners();
    }

    setupListeners() {
        this.removeReplyToButton.addEventListener('click', this.removeReplyTo.bind(this));
        this.hideFormButton.addEventListener('click', this.hideForm.bind(this));
        this.addCommentButton.addEventListener('click', this.showForm.bind(this));

        this.elem.addEventListener('page-comment-delete', () => {
            this.updateCount();
            this.hideForm();
        });

        this.elem.addEventListener('page-comment-reply', event => {
            this.setReply(event.detail.id, event.detail.element);
        });

        if (this.form) {
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

        const text = this.formInput.value;
        const reqData = {
            text,
            parent_id: this.parentId || null,
        };

        window.$http.post(`/comment/${this.pageId}`, reqData).then(resp => {
            const newElem = htmlToDom(resp.data);
            this.formContainer.after(newElem);
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
        this.commentsTitle.textContent = window.trans_plural(this.countText, count, {count});
    }

    resetForm() {
        this.formInput.value = '';
        this.removeReplyTo();
        this.container.append(this.formContainer);
    }

    showForm() {
        this.formContainer.toggleAttribute('hidden', false);
        this.addButtonContainer.toggleAttribute('hidden', true);
        setTimeout(() => {
            this.formInput.focus();
        }, 100);
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

    getCommentCount() {
        return this.container.querySelectorAll('[component="page-comment"]').length;
    }

    setReply(commentLocalId, commentElement) {
        const targetFormLocation = commentElement.closest('.comment-branch').querySelector('.comment-branch-children');
        this.showForm();
        targetFormLocation.append(this.formContainer);
        this.formContainer.scrollIntoView({behavior: 'smooth', block: 'nearest'});
        this.parentId = commentLocalId;
        this.replyToRow.toggleAttribute('hidden', false);
        const replyLink = this.replyToRow.querySelector('a');
        replyLink.textContent = `#${this.parentId}`;
        replyLink.href = `#comment${this.parentId}`;
    }

    removeReplyTo() {
        this.parentId = null;
        this.replyToRow.toggleAttribute('hidden', true);
    }

}

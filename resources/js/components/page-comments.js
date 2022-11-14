import {scrollAndHighlightElement} from "../services/util";

/**
 * @extends {Component}
 */
class PageComments {

    setup() {
        this.elem = this.$el;
        this.pageId = Number(this.$opts.pageId);

        // Element references
        this.container = this.$refs.commentContainer;
        this.formContainer = this.$refs.formContainer;
        this.commentCountBar = this.$refs.commentCountBar;
        this.addButtonContainer = this.$refs.addButtonContainer;
        this.replyToRow = this.$refs.replyToRow;

        // Translations
        this.updatedText = this.$opts.updatedText;
        this.deletedText = this.$opts.deletedText;
        this.createdText = this.$opts.createdText;
        this.countText = this.$opts.countText;

        // Internal State
        this.editingComment = null;
        this.parentId = null;

        if (this.formContainer) {
            this.form = this.formContainer.querySelector('form');
            this.formInput = this.form.querySelector('textarea');
            this.form.addEventListener('submit', this.saveComment.bind(this));
        }

        this.elem.addEventListener('click', this.handleAction.bind(this));
        this.elem.addEventListener('submit', this.updateComment.bind(this));
    }

    handleAction(event) {
        let actionElem = event.target.closest('[action]');

        if (event.target.matches('a[href^="#"]')) {
            const id = event.target.href.split('#')[1];
            scrollAndHighlightElement(document.querySelector('#' + id));
        }

        if (actionElem === null) return;
        event.preventDefault();

        const action = actionElem.getAttribute('action');
        const comment = actionElem.closest('[comment]');
        if (action === 'edit') this.editComment(comment);
        if (action === 'closeUpdateForm') this.closeUpdateForm();
        if (action === 'delete') this.deleteComment(comment);
        if (action === 'addComment') this.showForm();
        if (action === 'hideForm') this.hideForm();
        if (action === 'reply') this.setReply(comment);
        if (action === 'remove-reply-to') this.removeReplyTo();
    }

    closeUpdateForm() {
        if (!this.editingComment) return;
        this.editingComment.querySelector('[comment-content]').style.display = 'block';
        this.editingComment.querySelector('[comment-edit-container]').style.display = 'none';
    }

    editComment(commentElem) {
        this.hideForm();
        if (this.editingComment) this.closeUpdateForm();
        commentElem.querySelector('[comment-content]').style.display = 'none';
        commentElem.querySelector('[comment-edit-container]').style.display = 'block';
        let textArea = commentElem.querySelector('[comment-edit-container] textarea');
        let lineCount = textArea.value.split('\n').length;
        textArea.style.height = ((lineCount * 20) + 40) + 'px';
        this.editingComment = commentElem;
    }

    updateComment(event) {
        let form = event.target;
        event.preventDefault();
        let text = form.querySelector('textarea').value;
        let reqData = {
            text: text,
            parent_id: this.parentId || null,
        };
        this.showLoading(form);
        let commentId = this.editingComment.getAttribute('comment');
        window.$http.put(`/comment/${commentId}`, reqData).then(resp => {
            let newComment = document.createElement('div');
            newComment.innerHTML = resp.data;
            this.editingComment.innerHTML = newComment.children[0].innerHTML;
            window.$events.success(this.updatedText);
            window.$components.init(this.editingComment);
            this.closeUpdateForm();
            this.editingComment = null;
        }).catch(window.$events.showValidationErrors).then(() => {
            this.hideLoading(form);
        });
    }

    deleteComment(commentElem) {
        let id = commentElem.getAttribute('comment');
        this.showLoading(commentElem.querySelector('[comment-content]'));
        window.$http.delete(`/comment/${id}`).then(resp => {
            commentElem.parentNode.removeChild(commentElem);
            window.$events.success(this.deletedText);
            this.updateCount();
            this.hideForm();
        });
    }

    saveComment(event) {
        event.preventDefault();
        event.stopPropagation();
        let text = this.formInput.value;
        let reqData = {
            text: text,
            parent_id: this.parentId || null,
        };
        this.showLoading(this.form);
        window.$http.post(`/comment/${this.pageId}`, reqData).then(resp => {
            let newComment = document.createElement('div');
            newComment.innerHTML = resp.data;
            let newElem = newComment.children[0];
            this.container.appendChild(newElem);
            window.$components.init(newElem);
            window.$events.success(this.createdText);
            this.resetForm();
            this.updateCount();
        }).catch(err => {
            window.$events.showValidationErrors(err);
            this.hideLoading(this.form);
        });
    }

    updateCount() {
        let count = this.container.children.length;
        this.elem.querySelector('[comments-title]').textContent = window.trans_plural(this.countText, count, {count});
    }

    resetForm() {
        this.formInput.value = '';
        this.formContainer.appendChild(this.form);
        this.hideForm();
        this.removeReplyTo();
        this.hideLoading(this.form);
    }

    showForm() {
        this.formContainer.style.display = 'block';
        this.formContainer.parentNode.style.display = 'block';
        this.addButtonContainer.style.display = 'none';
        this.formInput.focus();
        this.formInput.scrollIntoView({behavior: "smooth"});
    }

    hideForm() {
        this.formContainer.style.display = 'none';
        this.formContainer.parentNode.style.display = 'none';
        if (this.getCommentCount() > 0) {
            this.elem.appendChild(this.addButtonContainer)
        } else {
            this.commentCountBar.appendChild(this.addButtonContainer);
        }
        this.addButtonContainer.style.display = 'block';
    }

    getCommentCount() {
        return this.elem.querySelectorAll('.comment-box[comment]').length;
    }

    setReply(commentElem) {
        this.showForm();
        this.parentId = Number(commentElem.getAttribute('local-id'));
        this.replyToRow.style.display = 'block';
        const replyLink = this.replyToRow.querySelector('a');
        replyLink.textContent = `#${this.parentId}`;
        replyLink.href = `#comment${this.parentId}`;
    }

    removeReplyTo() {
        this.parentId = null;
        this.replyToRow.style.display = 'none';
    }

    showLoading(formElem) {
        const groups = formElem.querySelectorAll('.form-group');
        for (let group of groups) {
            group.style.display = 'none';
        }
        formElem.querySelector('.form-group.loading').style.display = 'block';
    }

    hideLoading(formElem) {
        const groups = formElem.querySelectorAll('.form-group');
        for (let group of groups) {
            group.style.display = 'block';
        }
        formElem.querySelector('.form-group.loading').style.display = 'none';
    }

}

export default PageComments;
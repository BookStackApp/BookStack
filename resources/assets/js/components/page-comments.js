import MarkdownIt from "markdown-it";
import {scrollAndHighlightElement} from "../services/util";

const md = new MarkdownIt({ html: false });

class PageComments {

    constructor(elem) {
        this.elem = elem;
        this.pageId = Number(elem.getAttribute('page-id'));
        this.editingComment = null;
        this.parentId = null;

        this.container = elem.querySelector('[comment-container]');
        this.formContainer = elem.querySelector('[comment-form-container]');

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

        let action = actionElem.getAttribute('action');
        if (action === 'edit') this.editComment(actionElem.closest('[comment]'));
        if (action === 'closeUpdateForm') this.closeUpdateForm();
        if (action === 'delete') this.deleteComment(actionElem.closest('[comment]'));
        if (action === 'addComment') this.showForm();
        if (action === 'hideForm') this.hideForm();
        if (action === 'reply') this.setReply(actionElem.closest('[comment]'));
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
            html: md.render(text),
            parent_id: this.parentId || null,
        };
        this.showLoading(form);
        let commentId = this.editingComment.getAttribute('comment');
        window.$http.put(window.baseUrl(`/ajax/comment/${commentId}`), reqData).then(resp => {
            let newComment = document.createElement('div');
            newComment.innerHTML = resp.data;
            this.editingComment.innerHTML = newComment.children[0].innerHTML;
            window.$events.emit('success', window.trans('entities.comment_updated_success'));
            window.components.init(this.editingComment);
            this.closeUpdateForm();
            this.editingComment = null;
            this.hideLoading(form);
        });
    }

    deleteComment(commentElem) {
        let id = commentElem.getAttribute('comment');
        this.showLoading(commentElem.querySelector('[comment-content]'));
        window.$http.delete(window.baseUrl(`/ajax/comment/${id}`)).then(resp => {
            commentElem.parentNode.removeChild(commentElem);
            window.$events.emit('success', window.trans('entities.comment_deleted_success'));
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
            html: md.render(text),
            parent_id: this.parentId || null,
        };
        this.showLoading(this.form);
        window.$http.post(window.baseUrl(`/ajax/page/${this.pageId}/comment`), reqData).then(resp => {
            let newComment = document.createElement('div');
            newComment.innerHTML = resp.data;
            let newElem = newComment.children[0];
            this.container.appendChild(newElem);
            window.components.init(newElem);
            window.$events.emit('success', window.trans('entities.comment_created_success'));
            this.resetForm();
            this.updateCount();
        });
    }

    updateCount() {
        let count = this.container.children.length;
        this.elem.querySelector('[comments-title]').textContent = window.trans_choice('entities.comment_count', count, {count});
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
        this.elem.querySelector('[comment-add-button-container]').style.display = 'none';
        this.formInput.focus();
        this.formInput.scrollIntoView({behavior: "smooth"});
    }

    hideForm() {
        this.formContainer.style.display = 'none';
        this.formContainer.parentNode.style.display = 'none';
        const addButtonContainer = this.elem.querySelector('[comment-add-button-container]');
        if (this.getCommentCount() > 0) {
            this.elem.appendChild(addButtonContainer)
        } else {
            const countBar = this.elem.querySelector('[comment-count-bar]');
            countBar.appendChild(addButtonContainer);
        }
        addButtonContainer.style.display = 'block';
    }

    getCommentCount() {
        return this.elem.querySelectorAll('.comment-box[comment]').length;
    }

    setReply(commentElem) {
        this.showForm();
        this.parentId = Number(commentElem.getAttribute('local-id'));
        this.elem.querySelector('[comment-form-reply-to]').style.display = 'block';
        let replyLink = this.elem.querySelector('[comment-form-reply-to] a');
        replyLink.textContent = `#${this.parentId}`;
        replyLink.href = `#comment${this.parentId}`;
    }

    removeReplyTo() {
        this.parentId = null;
        this.elem.querySelector('[comment-form-reply-to]').style.display = 'none';
    }

    showLoading(formElem) {
        let groups = formElem.querySelectorAll('.form-group');
        for (let i = 0, len = groups.length; i < len; i++) {
            groups[i].style.display = 'none';
        }
        formElem.querySelector('.form-group.loading').style.display = 'block';
    }

    hideLoading(formElem) {
        let groups = formElem.querySelectorAll('.form-group');
        for (let i = 0, len = groups.length; i < len; i++) {
            groups[i].style.display = 'block';
        }
        formElem.querySelector('.form-group.loading').style.display = 'none';
    }

}

export default PageComments;
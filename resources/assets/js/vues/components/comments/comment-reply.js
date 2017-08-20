const MarkdownIt = require("markdown-it");
const md = new MarkdownIt({ html: true });

var template = `
<div class="comment-editor" v-cloak>
<form novalidate>
    <textarea name="markdown" rows="3" v-model="comment.text" :placeholder="trans('entities.comment_placeholder')"></textarea>
    <input type="hidden" v-model="comment.pageId" name="comment.pageId" :value="pageId">
    <button type="button" v-if="isReply || isEdit" class="button muted" v-on:click="closeBox">{{ trans('entities.comment_cancel') }}</button>
    <button type="submit" class="button pos" v-on:click.prevent="saveComment">{{ trans('entities.comment_save') }}</button>
</form>
</div>
`;

const props = {
    pageId: {},
    commentObj: {},
    isReply: {
        default: false,
        type: Boolean
    }, isEdit: {
        default: false,
        type: Boolean
    }
};

function data() {
    let comment = {
        text: ''
    };

    if (this.isReply) {
        comment.page_id = this.commentObj.page_id;
        comment.id = this.commentObj.id;
    } else if (this.isEdit) {
        comment = this.commentObj;
    }

    return {
        comment: comment,
        trans: trans
    };
}

const methods = {
    saveComment: function (event) {
        let pageId = this.comment.page_id || this.pageId;
        let commentText = this.comment.text;
        if (!commentText) {
            return this.$events.emit('error', trans('errors.empty_comment'))
        }
        let commentHTML = md.render(commentText);
        let serviceUrl = `/ajax/page/${pageId}/comment/`;
        let httpMethod = 'post';
        let reqObj = {
            text: commentText,
            html: commentHTML
        };

        if (this.isEdit === true) {
            // this will be set when editing the comment.
            serviceUrl = `/ajax/page/${pageId}/comment/${this.comment.id}`;
            httpMethod = 'put';
        } else if (this.isReply === true) {
            // if its reply, get the parent comment id
            reqObj.parent_id = this.comment.id;
        }
        $http[httpMethod](window.baseUrl(serviceUrl), reqObj).then(resp => {
            if (!isCommentOpSuccess(resp)) {
                this.$events.emit('error', getErrorMsg(resp));
                return;
            }
            // hide the comments first, and then retrigger the refresh
            if (this.isEdit) {
                this.$emit('comment-edited', event, resp.data.comment);
            } else {
                this.comment.text = '';
                this.$emit('comment-added', event);
                if (this.isReply === true) {
                    this.$emit('comment-replied', event, resp.data.comment);
                } else {
                    this.$parent.$emit('new-comment', event, resp.data.comment);
                }
            }
            this.$events.emit('success', resp.data.message);
        }).catch(err => {
            this.$events.emit('error', trans('errors.comment_add'))
        });
    },
    closeBox: function (event) {
        this.$emit('editor-removed', event);
    }
};

const computed = {};

function isCommentOpSuccess(resp) {
    if (resp && resp.data && resp.data.status === 'success') {
        return true;
    }
    return false;
}

function getErrorMsg(response) {
    if (response.data) {
        return response.data.message;
    } else {
        return trans('errors.comment_add');
    }
}

module.exports = { name: 'comment-reply', template, data, props, methods, computed };


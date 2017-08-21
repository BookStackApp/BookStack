const comment = require('./components/comments/comment');
const commentReply = require('./components/comments/comment-reply');

let data = {
    totalCommentsStr: trans('entities.comments_loading'),
    comments: [],
    permissions: null,
    currentUserId: null,
    trans: trans,
    commentCount: 0
};

let methods = {
    commentAdded: function () {
        ++this.totalComments;
    }
}

let computed = {
    totalComments: {
        get: function () {
            return this.commentCount;
        },
        set: function (value) {
            this.commentCount = value;
            if (value === 0) {
                this.totalCommentsStr = trans('entities.no_comments');
            } else if (value === 1) {
                this.totalCommentsStr = trans('entities.one_comment');
            } else {
                this.totalCommentsStr = trans('entities.x_comments', {
                    numComments: value
                });
            }
        }
    },
    canComment: function () {
        if (!this.permissions) {
            return false;
        }
        return this.permissions.comment_create === true;
    }
}

function mounted() {
    this.pageId = Number(this.$el.getAttribute('page-id'));
    // let linkedCommentId = this.$route.query.cm;
    let linkedCommentId = getUrlParameter('cm');
    this.$http.get(window.baseUrl(`/ajax/page/${this.pageId}/comments/`)).then(resp => {
        if (!isCommentOpSuccess(resp)) {
            // just show that no comments are available.
            vm.totalComments = 0;
            this.$events.emit('error', getErrorMsg(resp));
            return;
        }
        this.comments = resp.data.comments;
        this.totalComments = +resp.data.total;
        this.permissions = resp.data.permissions;
        this.currentUserId = resp.data.user_id;
        if (!linkedCommentId) {
            return;
        }

        // adding a setTimeout to give comment list some time to render.
        setTimeout(function() {
            focusLinkedComment(linkedCommentId);
        });
    }).catch(err => {
        this.$events.emit('error', trans('errors.comment_list'));
    });
}

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

function created() {
    this.$on('new-comment', function (event, comment) {
        this.comments.push(comment);
    })
}

function beforeDestroy() {
    this.$off('new-comment');
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.hash);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

function focusLinkedComment(linkedCommentId) {
    let comment = document.getElementById(linkedCommentId);
    if (comment && comment.length === 0) {
        return;
    }

    window.setupPageShow.goToText(linkedCommentId);
}

module.exports = {
    data, methods, mounted, computed, components: {
        comment, commentReply
    },
    created, beforeDestroy
};
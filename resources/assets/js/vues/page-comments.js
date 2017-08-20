const comment = require('./components/comments/comment');
const commentReply = require('./components/comments/comment-reply');

// 1. Remove code from controllers
// 2. Remove code from services.
// 3.

let data = {
  totalCommentsStr: trans('entities.comments_loading'),
  comments: [],
  permissions: null,
  current_user_id: null,
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
    return true;
  }
}

function mounted() {
  this.pageId = Number(this.$el.getAttribute('page-id'));
  // let linkedCommentId = this.$route.query.cm;
  let linkedCommentId = null;
  this.$http.get(window.baseUrl(`/ajax/page/${this.pageId}/comments/`)).then(resp => {
    if (!isCommentOpSuccess(resp)) {
        // just show that no comments are available.
        vm.totalComments = 0;
        return;
    }
    this.comments = resp.data.comments;
    this.totalComments = +resp.data.total;
    this.permissions = resp.data.permissions;
    this.current_user_id = resp.data.user_id;
    if (!linkedCommentId) {
        return;
    }
    $timeout(function() {
        // wait for the UI to render.
        focusLinkedComment(linkedCommentId);
    });
  }, checkError('errors.comment_list'));
}

function isCommentOpSuccess(resp) {
  if (resp && resp.data && resp.data.status === 'success') {
      return true;
  }
  return false;
}

function checkError(msgKey) {
  return function(response) {
    let msg = null;
    if (isCommentOpSuccess(response)) {
        // all good
        return;
    } else if (response.data) {
        msg = response.data.message;
    } else {
        msg = trans(msgKey);
    }
    if (msg) {
        events.emit('success', msg);
    }
  }
}

function created () {
  this.$on('new-comment', function (event, comment) {
    this.comments.push(comment);
  })
}

function beforeDestroy() {
  this.$off('new-comment');
}

module.exports = {
  data, methods, mounted, computed, components : {
    comment, commentReply
  },
  created, beforeDestroy
};
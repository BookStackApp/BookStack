const commentReply = require('./comment-reply');

const template = `
<div class="comment-box">
  <div class='page-comment' :id="commentId">
  <div class="user-image">
      <img :src="comment.created_by.avatar_url" alt="user avatar">
  </div>
  <div class="comment-container">
      <div class="comment-header">
          <a :href="comment.created_by.profile_url">{{comment.created_by.name}}</a>
      </div>
      <div v-html="comment.html" v-if="comment.active" class="comment-body" v-bind:class="{ 'comment-inactive' : !comment.active }">

      </div>
      <div v-if="!comment.active" class="comment-body comment-inactive">
          {{ trans('entities.comment_deleted') }}
      </div>
      <div class="comment-actions">
          <ul>
              <li v-if="(level < 4 && canComment)">
                <a href="#" comment="comment" v-on:click.prevent="replyComment">{{ trans('entities.comment_reply') }}</a>
              </li>
              <li v-if="canEditOrDelete('update')">
                <a href="#" comment="comment" v-on:click.prevent="editComment">{{ trans('entities.comment_edit') }}</a>
              </li>
              <li v-if="canEditOrDelete('delete')">
                <a href="#" comment="comment" v-on:click.prevent="deleteComment">{{ trans('entities.comment_delete') }}</a>
              </li>
              <li>{{ trans('entities.comment_create') }}
                <a :title="comment.created.day_time_str" :href="commentHref">{{comment.created.diff}}</a>
              </li>
              <li v-if="comment.updated">
                <span :title="comment.updated.day_time_str">{{trans('entities.comment_updated_text', { updateDiff: comment.updated.diff }) }}
                      <a :href="comment.updated_by.profile_url">{{comment.updated_by.name}}</a>
                </span>
              </li>
          </ul>
      </div>
      <div v-if="showEditor && level <= 3">
        <comment-reply :page-id="comment.page_id" :comment-obj="comment"
          v-on:editor-removed.stop.prevent="hideComment"
          v-on:comment-replied.stop="commentReplied(...arguments)"
          v-on:comment-edited.stop="commentEdited(...arguments)"
          v-on:comment-added.stop="commentAdded"
           :is-reply="isReply" :is-edit="isEdit">
        </comment-reply>
      </div>
      <comment v-for="(comment, index) in comments" :initial-comment="comment" :index="index"
        :level="nextLevel" :key="comment.id" :permissions="permissions" :current-user-id="currentUserId"
        v-on:comment-added.stop="commentAdded"></comment>

  </div>
  </div>
</div>
`;

const props = ['initialComment', 'index', 'level', 'permissions', 'currentUserId'];

function data () {
  return {
    trans: trans,
    commentHref: null,
    comments: [],
    showEditor: false,
    comment: this.initialComment,
    nextLevel: this.level + 1
  };
}

const methods = {
  deleteComment: function () {
    var resp = window.confirm(trans('entities.comment_delete_confirm'));
    if (!resp) {
        return;
    }
    this.$http.delete(window.baseUrl(`/ajax/comment/${this.comment.id}`)).then(resp => {
      if (!isCommentOpSuccess(resp)) {
          return;
      }
      updateComment(this.comment, resp.data, true);
    }, function (resp) {
      if (isCommentOpSuccess(resp)) {
          this.$events.emit('success', trans('entities.comment_deleted'));
      } else {
          this.$events.emit('error', trans('error.comment_delete'));
      }
    });
  },
  replyComment: function () {
    this.toggleEditor(false);
  },
  editComment: function () {
    this.toggleEditor(true);
  },
  hideComment: function () {
    this.showEditor = false;
  },
  toggleEditor: function (isEdit) {
    this.showEditor = false;
    this.isEdit = isEdit;
    this.isReply = !isEdit;
    this.showEditor = true;
  },
  commentReplied: function (event, comment) {
    this.comments.push(comment);
    this.showEditor = false;
  },
  commentEdited: function (event, comment) {
    this.comment = comment;
    this.showEditor = false;
  },
  commentAdded: function (event, comment) {
    // this is to handle non-parent child relationship
    // we want to make it go up.
    this.$emit('comment-added', event);
  },
  canEditOrDelete: function (prop) {
    if (!this.comment.active) {
      return false;
    }

    if (!this.permissions) {
      return false;
    }

    let propAll = 'comment_' + prop + '_all';
    let propOwn = 'comment_' + prop + '_own';

    if (this.permissions[propAll]) {
        return true;
    }

    if (this.permissions[propOwn] && this.comment.created_by.id === this.currentUserId) {
        return true;
    }

    return false;
  },
  canComment: function () {
    if (!this.permissions) {
      return false;
    }
    return this.permissions.comment_create === true;
  }
};

const computed = {
  commentId: {
    get: function () {
      return `comment-${this.comment.page_id}-${this.comment.id}`;
    },
    set: function () {
      this.commentHref = `#?cm=${this.commentId}`
    }
  }
};

function mounted () {
  if (this.comment.sub_comments && this.comment.sub_comments.length) {
    // set this so that we can render the next set of sub comments.
    this.comments = this.comment.sub_comments;
  }
}

function isCommentOpSuccess(resp) {
  if (resp && resp.data && resp.data.status === 'success') {
      return true;
  }
  return false;
}

function updateComment(comment, resp, isDelete) {
  comment.text = resp.comment.text;
  comment.updated = resp.comment.updated;
  comment.updated_by = resp.comment.updated_by;
  comment.active = resp.comment.active;
  if (isDelete && !resp.comment.active) {
      comment.html = trans('entities.comment_deleted');
  } else {
      comment.html = resp.comment.html;
  }
}

module.exports = {
  name: 'comment',
  template, data, props, methods, computed, mounted, components: {
  commentReply
}};


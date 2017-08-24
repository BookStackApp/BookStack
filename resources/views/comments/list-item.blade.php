<div class='page-comment' id="comment-@{{::pageId}}-@{{::comment.id}}">
    <div class="user-image">
        <img ng-src="@{{::comment.created_by.avatar_url}}" alt="user avatar">
    </div>
    <div class="comment-container">
        <div class="comment-header">
            <a href="@{{::comment.created_by.profile_url}}">@{{ ::comment.created_by.name }}</a>
        </div>
        <div ng-bind-html="comment.html" ng-if="::comment.active" class="comment-body" ng-class="!comment.active ? 'comment-inactive' : ''">

        </div>
        <div ng-if="::!comment.active" class="comment-body comment-inactive">
            {{ trans('entities.comment_deleted') }}
        </div>
        <div class="comment-actions">
            <ul ng-if="!comment.is_hidden">
                <li ng-if="::(level < 3 && vm.canComment())"><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment" is-reply="true">{{ trans('entities.comment_reply') }}</a></li>
                <li ng-if="::vm.canEditDelete(comment, 'comment_update')"><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment" >{{ trans('entities.comment_edit') }}</a></li>
                <li ng-if="::vm.canEditDelete(comment, 'comment_delete')"><a href="#" comment-delete-link comment="comment" >{{ trans('entities.comment_delete') }}</a></li>
                <li>{{ trans('entities.comment_create') }} <a title="@{{::comment.created.day_time_str}}" href="#?cm=comment-@{{::pageId}}-@{{::comment.id}}">@{{::comment.created.diff}}</a></li>
                <li ng-if="::comment.updated"><span title="@{{::comment.updated.day_time_str}}">@{{ ::vm.trans('entities.comment_updated_text', { updateDiff: comment.updated.diff }) }}
                        <a href="@{{::comment.updated_by.profile_url}}">@{{::comment.updated_by.name}}</a></span></li>
            </ul>
        </div>
        <div class="comment-box" ng-repeat="comment in comments = comment.sub_comments track by comment.id" ng-init="level = level + 1">
            <div ng-include src="'comment-list-item.html'">
            </div>
        </div>
    </div>
</div>
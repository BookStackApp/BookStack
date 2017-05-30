<div class='page-comment' id="comment-@{{::pageId}}-@{{::comment.id}}">
    <div class="user-image">
        <img ng-src="@{{::comment.created_by.avatar_url}}" alt="user avatar">
    </div>
    <div class="comment-container">
        <div class="comment-header">
            <a href="@{{::comment.created_by.profile_url}}">@{{ ::comment.created_by.name }}</a>
        </div>
        <div ng-bind-html="comment.html" class="comment-body">

        </div>
        <div class="comment-actions">
            <ul>
                <li ng-if="level < 3"><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment" is-reply="true">Reply</a></li>
                <li><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment">Edit</a></li>
                <li>Created <a title="@{{::comment.created.day_time_str}}" href="#comment-@{{::comment.id}}-@{{::pageId}}">@{{::comment.created.diff}}</a></li>
                <li ng-if="comment.updated"><span title="@{{comment.updated.day_time_str}}">Updated @{{comment.updated.diff}} by
                    <a href="@{{comment.updated_by.profile_url}}">@{{comment.updated_by.name}}</a></span></li>
            </ul>
        </div>
        <div class="comment-box" ng-repeat="comment in comments = comment.sub_comments track by comment.id" ng-init="level = level + 1">
            <div ng-include src="'comment-list-item.html'">
            </div>
        </div>
    </div>
</div>
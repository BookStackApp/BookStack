<div class='page-comment' id="comment-@{{::pageId}}-@{{::comment.id}}">
    <div class="user-image">
        <img ng-src="@{{::defaultAvatar}}" alt="user avatar">
    </div>
    <div class="comment-container">
        <div class="comment-header">
            @{{ ::comment.created_by_name }}
        </div>
        <div ng-bind-html="comment.html" class="comment-body">

        </div>
        <div class="comment-actions" ng-class="{'has-border': comment.cnt_sub_comments === 0 || comment.is_loaded}">
            <ul>
                <li ng-if="level < 3"><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment" is-reply="true">Reply</a></li>
                <li><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment">Edit</a></li>
                <li>Created <a title="@{{::comment.created.day_time_str}}" href="#comment-@{{::comment.id}}-@{{::pageId}}">@{{::comment.created.diff}}</a></li>
                <li ng-if="comment.updated"><span title="@{{::comment.updated.day_time_str}}">Updated @{{::comment.updated.diff}}</span></li>
            </ul>
        </div>
        <div class="load-more-comments" ng-if="comment.cnt_sub_comments > 0 && !comment.is_loaded">
            <a href="#" ng-click="vm.loadSubComments($event, comment, $index)">
                Load @{{::comment.cnt_sub_comments}} more comment(s)
            </a>
        </div>
        <div class="comment-box" ng-repeat="comment in comments = comment.comments track by comment.id" ng-init="level = level + 1">
            <div ng-include src="'comment-list-item.html'">
            </div>
        </div>
    </div>
</div>
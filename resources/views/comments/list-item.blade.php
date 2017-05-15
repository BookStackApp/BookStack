<div class='page-comment'>
    <div class="user-image">
        <img ng-src="@{{::defaultAvatar}}" alt="user avatar">
    </div>
    <div class="comment-container">
        <div class="comment-header">
            @{{ ::comment.created_by_name }}
        </div>
        <div ng-bind-html="comment.html" class="comment-body">

        </div>
        <div class="comment-actions">
            <ul>
                <li><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment" is-reply="true">Reply</a></li>
                <li><a href="#" comment-reply-link no-comment-reply-dupe="true" comment="comment">Edit</a></li>
                <li><a href="#">@{{::comment.created_at}}</a></li>            
            </ul>                
        </div>
        <a href="#" ng-click="vm.loadSubComments($event, comment, $index)" class="load-more-comments" ng-if="comment.cnt_sub_comments > 0 && !comment.is_loaded">
            Load @{{::comment.cnt_sub_comments}} more comment(s)
        </a>        
        <div class="comment-box" ng-repeat="comment in comments = comment.comments track by comment.id">        
            <div ng-include src="'comment-list-item.html'">
            </div>
        </div>        
    </div>    
</div>
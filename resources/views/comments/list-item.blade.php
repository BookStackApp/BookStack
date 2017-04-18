<div class='page-comment'>
    <div class="user-image">
        <img ng-src="@{{defaultAvatar}}" alt="user avatar">
    </div>
    <div class="comment-container">
        <div class="comment-header">
            @{{ ::comment.created_by_name }}
        </div>
        <div ng-bind-html="comment.html" class="comment-body">

        </div>
        <div class="comment-actions">
            <ul>
                <li><a href="#">Reply</a></li>
                <li><a href="#">@{{::comment.created_at}}</a></li>            
            </ul>                
        </div>
    </div>
</div>
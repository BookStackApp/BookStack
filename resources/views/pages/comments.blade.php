<div ng-controller="CommentListController as vm" ng-init="pageId = <?= $page->id ?>" class="comments-list" ng-cloak>   
<h3>@{{vm.totalCommentsStr}}</h3>
<hr> 
    <div class="comment-box" ng-repeat="comment in vm.comments track by comment.id">
        @include('comments/list-item')
    </div>
</div>
@include('comments/add', ['pageId' => $pageId])

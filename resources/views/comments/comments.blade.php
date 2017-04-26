@section('head')
    <script src="{{ baseUrl("/libs/simplemde/simplemde.min.js") }}"></script>
@stop
<script type="text/ng-template" id="comment-list-item.html">
    @include('comments/list-item')
</script>
<script type="text/ng-template" id="comment-reply.html">
    @include('comments/comment-reply')
</script>
<div ng-controller="CommentListController as vm" ng-init="pageId = <?= $page->id ?>" class="comments-list" ng-cloak>   
<h3>@{{vm.totalCommentsStr}}</h3>
<hr> 
    <div class="comment-box" ng-repeat="comment in vm.comments track by comment.id">        
        <div ng-include src="'comment-list-item.html'">
            
        </div>
    </div>
</div>
@include('comments/add', ['pageId' => $pageId])
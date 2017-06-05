<div class="comment-editor" ng-controller="CommentReplyController as vm" ng-cloak>
    <form novalidate>
        <textarea name="markdown" rows="3" ng-model="comment.text" placeholder="{{ trans('entities.comment_placeholder') }}"></textarea>
        <input type="hidden" ng-model="comment.pageId" name="comment.pageId" value="{{$pageId}}" ng-init="comment.pageId = {{$pageId }}">
        <button type="button" ng-if="::(isReply || isEdit)" class="button muted" ng-click="closeBox()">{{ trans('entities.comment_cancel') }}</button>
        <button type="submit" class="button pos" ng-click="vm.saveComment(isReply)">{{ trans('entities.comment_save') }}</button>
    </form>
</div>

@if($errors->has('markdown'))
    <div class="text-neg text-small">{{ $errors->first('markdown') }}</div>
@endif
<!-- TODO :: needs to be merged with add.blade.php -->
<form novalidate>
        <div simple-markdown-input smd-model="comment.newComment" smd-get-content="getCommentHTML" smd-clear="clearInput">
            <textarea name="markdown" rows="3"
                      @if($errors->has('markdown')) class="neg" @endif>@if(isset($model) ||
                      old('markdown')){{htmlspecialchars( old('markdown') ? old('markdown') : ($model->markdown === '' ? $model->html : $model->markdown))}}@endif</textarea>
        </div>
        <input type="hidden" ng-model="pageId" name="comment.pageId" value="{{$pageId}}" ng-init="comment.pageId = {{$pageId }}">
        <button type="submit" class="button pos" ng-click="vm.saveComment()">Save</button>
</form>
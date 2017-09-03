<div page-comments page-id="{{ $page->id }}" ng-non-bindable class="comments-list">
  <h3 comments-title>{{ trans_choice('entities.comment_count', count($page->comments), ['count' => count($page->comments)]) }}</h3>

    <div class="comment-container" comment-container>
        @foreach($page->comments as $comment)
            @include('comments.comment', ['comment' => $comment])
        @endforeach
    </div>


    @if(userCan('comment-create-all'))

        <div class="comment-box" comment-box style="display:none;">
            <div class="header"><i class="zmdi zmdi-comment"></i> {{ trans('entities.comment_new') }}</div>
            <div class="content" comment-form-container>
                <form novalidate>
                    <div class="form-group">
                        <textarea name="markdown" rows="3" v-model="comment.text" placeholder="{{ trans('entities.comment_placeholder') }}"></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="button outline" action="hideForm">{{ trans('common.cancel') }}</button>
                        <button type="submit" class="button pos">{{ trans('entities.comment_save') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="form-group" comment-add-button>
            <button type="button" action="addComment" class="button outline">Add Comment</button>
        </div>
    @endif

</div>
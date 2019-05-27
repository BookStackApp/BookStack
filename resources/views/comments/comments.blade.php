<div page-comments page-id="{{ $page->id }}" class="comments-list">
    <div comment-count-bar class="grid half left-focus v-center no-row-gap">
        <h5 comments-title>{{ trans_choice('entities.comment_count', count($page->comments), ['count' => count($page->comments)]) }}</h5>
        @if (count($page->comments) === 0)
            <div class="text-m-right" comment-add-button-container>
                <button type="button" action="addComment"
                        class="button outline">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    </div>

    <div class="comment-container" comment-container>
        @foreach($page->comments as $comment)
            @include('comments.comment', ['comment' => $comment])
        @endforeach
    </div>

    @if(userCan('comment-create-all'))
        @include('comments.create')
    @endif

    @if (count($page->comments) > 0)
        <div class="text-right" comment-add-button-container>
            <button type="button" action="addComment"
                    class="button outline">{{ trans('entities.comment_add') }}</button>
        </div>
    @endif

</div>
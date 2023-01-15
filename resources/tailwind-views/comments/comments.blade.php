<section component="page-comments"
         option:page-comments:page-id="{{ $page->id }}"
         option:page-comments:updated-text="{{ trans('entities.comment_updated_success') }}"
         option:page-comments:deleted-text="{{ trans('entities.comment_deleted_success') }}"
         option:page-comments:created-text="{{ trans('entities.comment_created_success') }}"
         option:page-comments:count-text="{{ trans('entities.comment_count') }}"
         class="comments-list"
         aria-label="{{ trans('entities.comments') }}">

    <div refs="page-comments@commentCountBar" class="grid half left-focus v-center no-row-gap">
        <h5 comments-title>{{ trans_choice('entities.comment_count', count($page->comments), ['count' => count($page->comments)]) }}</h5>
        @if (count($page->comments) === 0 && userCan('comment-create-all'))
            <div class="text-m-right" refs="page-comments@addButtonContainer">
                <button type="button" action="addComment"
                        class="button outline">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    </div>

    <div refs="page-comments@commentContainer" class="comment-container">
        @foreach($page->comments as $comment)
            @include('comments.comment', ['comment' => $comment])
        @endforeach
    </div>

    @if(userCan('comment-create-all'))
        @include('comments.create')

        @if (count($page->comments) > 0)
            <div refs="page-comments@addButtonContainer" class="text-right">
                <button type="button" action="addComment"
                        class="button outline">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    @endif

</section>
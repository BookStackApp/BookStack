<div page-comments page-id="{{ $page->id }}" class="comments-list">
    <h5 comments-title>{{ trans_choice('entities.comment_count', count($page->comments), ['count' => count($page->comments)]) }}</h5>

    <div class="comment-container" comment-container>
        @foreach($page->comments as $comment)
            @include('comments.comment', ['comment' => $comment])
        @endforeach
    </div>

    @if(userCan('comment-create-all'))
        @include('comments.create')
    @endif

</div>
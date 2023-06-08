<div class="comment-branch">
    <div class="mb-m">
        @include('comments.comment', ['comment' => $branch['comment']])
    </div>
    <div class="flex-container-row">
        <div class="comment-thread-indicator-parent">
            <div class="comment-thread-indicator"></div>
        </div>
        <div class="comment-branch-children flex">
            @foreach($branch['children'] as $childBranch)
                @include('comments.comment-branch', ['branch' => $childBranch])
            @endforeach
        </div>
    </div>
</div>
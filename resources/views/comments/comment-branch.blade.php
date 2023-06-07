<div>
    <div class="mb-m">
        @include('comments.comment', ['comment' => $branch['comment']])
    </div>
    @if(count($branch['children']) > 0)
        <div class="flex-container-row">
            <div class="pb-m">
                <div class="comment-thread-indicator fill-height"></div>
            </div>
            <div class="flex">
                @foreach($branch['children'] as $childBranch)
                    @include('comments.comment-branch', ['branch' => $childBranch])
                @endforeach
            </div>
        </div>
    @endif
</div>
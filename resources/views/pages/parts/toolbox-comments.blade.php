{{--
$comments - CommentTree
--}}
<div refs="editor-toolbox@tab-content" data-tab-content="comments" class="toolbox-tab-content">
    <h4>{{ trans('entities.comments') }}</h4>

    <div class="comment-container-compact px-l">
        <p class="text-muted small mb-m">
            {{ trans('entities.comment_editor_explain') }}
        </p>
        @foreach($comments->getActive() as $branch)
            @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => true])
        @endforeach
        @if($comments->empty())
            <p class="italic text-muted">{{ trans('entities.comment_none') }}</p>
        @endif
        @if($comments->archivedThreadCount() > 0)
            <details class="section-expander mt-s">
                <summary>{{ trans('entities.comment_archived_threads') }}</summary>
                @foreach($comments->getArchived() as $branch)
                    @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => true])
                @endforeach
            </details>
        @endif
    </div>
</div>
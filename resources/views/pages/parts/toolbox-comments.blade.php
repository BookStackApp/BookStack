<div refs="editor-toolbox@tab-content" data-tab-content="comments" class="toolbox-tab-content">
    <h4>{{ trans('entities.comments') }}</h4>

    <div class="comment-container-compact px-l">
        <p class="text-muted small mb-m">
            {{ trans('entities.comment_editor_explain') }}
        </p>
        @foreach($comments->get() as $branch)
            @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => true])
        @endforeach
        @if($comments->empty())
            <p class="italic text-muted">{{ trans('common.no_items') }}</p>
        @endif
    </div>
</div>
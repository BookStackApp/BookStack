<section components="page-comments tabs"
         option:page-comments:page-id="{{ $page->id }}"
         option:page-comments:created-text="{{ trans('entities.comment_created_success') }}"
         option:page-comments:count-text="{{ trans('entities.comment_thread_count') }}"
         option:page-comments:archived-count-text="{{ trans('entities.comment_archived_count') }}"
         option:page-comments:wysiwyg-text-direction="{{ $locale->htmlDirection() }}"
         class="comments-list tab-container"
         aria-label="{{ trans('entities.comments') }}">

    <div refs="page-comments@comment-count-bar" class="flex-container-row items-center">
        <div role="tablist" class="flex">
            <button type="button"
                    role="tab"
                    id="comment-tab-active"
                    aria-controls="comment-tab-panel-active"
                    refs="page-comments@active-tab"
                    aria-selected="true">{{ trans_choice('entities.comment_thread_count', $commentTree->activeThreadCount()) }}</button>
            <button type="button"
                    role="tab"
                    id="comment-tab-archived"
                    aria-controls="comment-tab-panel-archived"
                    refs="page-comments@archived-tab"
                    aria-selected="false">{{ trans_choice('entities.comment_archived_count', count($commentTree->getArchived())) }}</button>
        </div>
        @if ($commentTree->empty() && userCan('comment-create-all'))
            <div refs="page-comments@add-button-container" class="ml-m flex-container-row" >
                <button type="button"
                        refs="page-comments@add-comment-button"
                        class="button outline mb-m ml-auto">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    </div>

    <div id="comment-tab-panel-active"
         refs="page-comments@active-container"
         tabindex="0"
         role="tabpanel"
         aria-labelledby="comment-tab-active"
         class="comment-container no-outline">
        <div refs="page-comments@comment-container">
            @foreach($commentTree->getActive() as $branch)
                @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => false])
            @endforeach
        </div>

        <p class="text-center text-muted italic empty-state">{{ trans('entities.comment_none') }}</p>

        @if(userCan('comment-create-all'))
            @include('comments.create')
            @if (!$commentTree->empty())
                <div refs="page-comments@addButtonContainer" class="ml-m flex-container-row">
                    <button type="button"
                            refs="page-comments@add-comment-button"
                            class="button outline mb-m ml-auto">{{ trans('entities.comment_add') }}</button>
                </div>
            @endif
        @endif
    </div>

    <div refs="page-comments@archive-container"
         id="comment-tab-panel-archived"
         tabindex="0"
         role="tabpanel"
         aria-labelledby="comment-tab-archived"
         hidden="hidden"
         class="comment-container no-outline">
        @foreach($commentTree->getArchived() as $branch)
            @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => false])
        @endforeach
            <p class="text-center text-muted italic empty-state">{{ trans('entities.comment_none') }}</p>
    </div>

    @if(userCan('comment-create-all') || $commentTree->canUpdateAny())
        @push('body-end')
            @include('form.editor-translations')
            @include('entities.selector-popup')
        @endpush
    @endif

</section>
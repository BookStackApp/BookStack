<section component="page-comments"
         option:page-comments:page-id="{{ $page->id }}"
         option:page-comments:created-text="{{ trans('entities.comment_created_success') }}"
         option:page-comments:count-text="{{ trans('entities.comment_count') }}"
         option:page-comments:wysiwyg-language="{{ $locale->htmlLang() }}"
         option:page-comments:wysiwyg-text-direction="{{ $locale->htmlDirection() }}"
         class="comments-list"
         aria-label="{{ trans('entities.comments') }}">

    <div refs="page-comments@comment-count-bar" class="grid half left-focus v-center no-row-gap">
        <h5 refs="page-comments@comments-title">{{ trans_choice('entities.comment_count', $commentTree->count(), ['count' => $commentTree->count()]) }}</h5>
        @if ($commentTree->empty() && userCan('comment-create-all'))
            <div class="text-m-right" refs="page-comments@add-button-container">
                <button type="button"
                        refs="page-comments@add-comment-button"
                        class="button outline">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    </div>

    <div refs="page-comments@comment-container" class="comment-container">
        @foreach($commentTree->getActive() as $branch)
            @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => false])
        @endforeach
    </div>

    @if(userCan('comment-create-all'))
        @include('comments.create')
        @if (!$commentTree->empty())
            <div refs="page-comments@addButtonContainer" class="flex-container-row">

                <button type="button"
                        refs="page-comments@show-archived-button"
                        class="text-button hover-underline">{{ trans_choice('entities.comment_archived', count($commentTree->getArchived())) }}</button>

                <button type="button"
                        refs="page-comments@add-comment-button"
                        class="button outline ml-auto">{{ trans('entities.comment_add') }}</button>
            </div>
        @endif
    @endif

    <div refs="page-comments@archive-container" class="comment-container">
        @foreach($commentTree->getArchived() as $branch)
            @include('comments.comment-branch', ['branch' => $branch, 'readOnly' => false])
        @endforeach
    </div>

    @if(userCan('comment-create-all') || $commentTree->canUpdateAny())
        @push('body-end')
            <script src="{{ versioned_asset('libs/tinymce/tinymce.min.js') }}" nonce="{{ $cspNonce }}" defer></script>
            @include('form.editor-translations')
            @include('entities.selector-popup')
        @endpush
    @endif

</section>
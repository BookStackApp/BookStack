<div component="image-manager"
     option:image-manager:uploaded-to="{{ $uploaded_to ?? 0 }}"
     class="image-manager">

    <div component="popup"
         refs="image-manager@popup"
         class="popup-background">
        <div class="popup-body" tabindex="-1">

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.image_select') }}</div>
                <button refs="popup@hide" type="button" class="popup-header-close">@icon('close')</button>
            </div>

            <div class="flex-fill image-manager-body">

                <div class="image-manager-content">
                    <div class="image-manager-header primary-background-light nav-tabs grid third no-gap">
                        <button refs="image-manager@filterTabs"
                                data-filter="all"
                                type="button" class="tab-item selected" title="{{ trans('components.image_all_title') }}">@icon('images') {{ trans('components.image_all') }}</button>
                        <button refs="image-manager@filterTabs"
                                data-filter="book"
                                type="button" class="tab-item" title="{{ trans('components.image_book_title') }}">@icon('book', ['class' => 'svg-icon']) {{ trans('entities.book') }}</button>
                        <button refs="image-manager@filterTabs"
                                data-filter="page"
                                type="button" class="tab-item" title="{{ trans('components.image_page_title') }}">@icon('page', ['class' => 'svg-icon']) {{ trans('entities.page') }}</button>
                    </div>
                    <div>
                        <form refs="image-manager@searchForm" class="contained-search-box">
                            <input refs="image-manager@searchInput"
                                   placeholder="{{ trans('components.image_search_hint') }}"
                                   type="text">
                            <button refs="image-manager@cancelSearch"
                                    title="{{ trans('common.search_clear') }}"
                                    type="button"
                                    class="cancel">@icon('close')</button>
                            <button type="submit" class="primary-background text-white"
                                    title="{{ trans('common.search') }}">@icon('search')</button>
                        </form>
                    </div>
                    <div refs="image-manager@listContainer" class="image-manager-list"></div>
                </div>

                <div class="image-manager-sidebar flex-container-column">

                    <div refs="image-manager@dropzoneContainer">
                        @include('form.dropzone', [
                            'placeholder' => trans('components.image_dropzone'),
                            'successMessage' => trans('components.image_upload_success'),
                            'url' => url('/images/gallery?' . http_build_query(['uploaded_to' => $uploaded_to ?? 0]))
                        ])
                    </div>

                    <div refs="image-manager@formContainer" class="inner flex"></div>
                </div>

            </div>

            <div class="popup-footer">
                <button refs="image-manager@selectButton" type="button" class="hidden button">
                    {{ trans('components.image_select_image') }}
                </button>
            </div>

        </div>
    </div>
</div>
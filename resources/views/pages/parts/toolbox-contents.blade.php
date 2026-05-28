<div refs="editor-toolbox@tab-content" data-tab-content="contents" class="toolbox-tab-content">
    <h4>{{ trans('entities.page_contents') }}</h4>

    <div component="toolbox-contents" class="comment-container-compact px-l">
        <p class="text-muted small mb-m">{{ trans('entities.page_contents_info') }}</p>
        <p class="text-muted small mb-m" refs="toolbox-contents@none" hidden="hidden">{{ trans('entities.page_contents_none') }}</p>
        <div refs="toolbox-contents@display"></div>
    </div>
</div>
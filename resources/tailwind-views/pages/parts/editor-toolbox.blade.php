<div component="editor-toolbox" class="floating-toolbox">

    <div class="tabs primary-background-light">
        <button type="button" refs="editor-toolbox@toggle" aria-expanded="false" class="toolbox-toggle">@icon('caret-left-circle')</button>
        <button type="button" refs="editor-toolbox@tab-button" data-tab="tags" title="{{ trans('entities.page_tags') }}" class="active">@icon('tag')</button>
        @if(userCan('attachment-create-all'))
            <button type="button" refs="editor-toolbox@tab-button" data-tab="files" title="{{ trans('entities.attachments') }}">@icon('attach')</button>
        @endif
        <button type="button" refs="editor-toolbox@tab-button" data-tab="templates" title="{{ trans('entities.templates') }}">@icon('template')</button>
    </div>

    <div refs="editor-toolbox@tab-content" data-tab-content="tags" class="toolbox-tab-content">
        <h4>{{ trans('entities.page_tags') }}</h4>
        <div class="px-l">
            @include('entities.tag-manager', ['entity' => $page])
        </div>
    </div>

    @if(userCan('attachment-create-all'))
        @include('attachments.manager', ['page' => $page])
    @endif

    <div refs="editor-toolbox@tab-content" data-tab-content="templates" class="toolbox-tab-content">
        <h4>{{ trans('entities.templates') }}</h4>

        <div class="px-l">
            @include('pages.parts.template-manager', ['page' => $page, 'templates' => $templates])
        </div>

    </div>

</div>

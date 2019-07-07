<div editor-toolbox class="floating-toolbox">

    <div class="tabs primary-background-light">
        <span toolbox-toggle>@icon('caret-left-circle')</span>
        <span toolbox-tab-button="tags" title="{{ trans('entities.page_tags') }}" class="active">@icon('tag')</span>
        @if(userCan('attachment-create-all'))
            <span toolbox-tab-button="files" title="{{ trans('entities.attachments') }}">@icon('attach')</span>
        @endif
        <span toolbox-tab-button="templates" title="{{ trans('entities.templates') }}">@icon('template')</span>
    </div>

    <div toolbox-tab-content="tags">
        <h4>{{ trans('entities.page_tags') }}</h4>
        <div class="px-l">
            @include('components.tag-manager', ['entity' => $page, 'entityType' => 'page'])
        </div>
    </div>

    @if(userCan('attachment-create-all'))
        @include('pages.attachment-manager', ['page' => $page])
    @endif

    <div toolbox-tab-content="templates">
        <h4>{{ trans('entities.templates') }}</h4>

        <div class="px-l">
            @include('pages.templates-manager', ['page' => $page])
        </div>


    </div>

</div>

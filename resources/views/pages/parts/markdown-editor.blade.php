<div id="markdown-editor" component="markdown-editor"
     option:markdown-editor:page-id="{{ $model->id ?? 0 }}"
     option:markdown-editor:text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     option:markdown-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:markdown-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-fill flex code-fill">

    <div class="markdown-editor-wrap active flex-container-column">
        <div class="editor-toolbar flex-container-row items-stretch justify-space-between">
            <div class="editor-toolbar-label text-mono px-m py-xs flex-container-row items-center flex">
                <span>{{ trans('entities.pages_md_editor') }}</span>
            </div>
            <div component="dropdown" class="buttons flex-container-row items-stretch editor-toolbar-stylings">
                <button class="text-button" type="button" data-action="insertHeadline" title="{{ trans('entities.pages_md_insert_headline') }}">@icon('headline')</button>
                <button class="text-button" type="button" data-action="insertBold" title="{{ trans('entities.pages_md_insert_bold') }}">@icon('bold')</button>
                <button class="text-button" type="button" data-action="insertItalic" title="{{ trans('entities.pages_md_insert_italic') }}">@icon('italic')</button>
                <button class="text-button" type="button" data-action="insertStrikethrough" title="{{ trans('entities.pages_md_insert_strikethrough') }}">@icon('strikethrough')</button>
                <button class="text-button" type="button" data-action="insertListBulleted" title="{{ trans('entities.pages_md_insert_list_bulleted') }}">@icon('list-bulleted')</button>
                <button class="text-button" type="button" data-action="insertListNumbered" title="{{ trans('entities.pages_md_insert_list_numbered') }}">@icon('list-numbered')</button>
                <button class="text-button" type="button" data-action="insertCode" title="{{ trans('entities.pages_md_insert_code') }}">@icon('code')</button>
                <button refs="dropdown@toggle" class="text-button" type="button" title="{{ trans('entities.pages_md_insert_snippet') }}">@icon('snippet')</button>
                <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                    <li><button type="button" data-action="insertTable" title="{{ trans('entities.pages_md_insert_table') }}">{{ trans('entities.pages_md_insert_table') }}</button></li>
                    <li><button type="button" data-action="insertCollapsibleSection" title="{{ trans('entities.pages_md_insert_collapsible_section') }}">{{ trans('entities.pages_md_insert_collapsible_section') }}</button></li>
                    <li><button type="button" data-action="insertCalloutInfo" title="{{ trans('entities.pages_md_insert_callout_info') }}">{{ trans('entities.pages_md_insert_callout_info') }}</button></li>
                    <li><button type="button" data-action="insertCalloutSuccess" title="{{ trans('entities.pages_md_insert_callout_success') }}">{{ trans('entities.pages_md_insert_callout_success') }}</button></li>
                    <li><button type="button" data-action="insertCalloutWarning" title="{{ trans('entities.pages_md_insert_callout_warning') }}">{{ trans('entities.pages_md_insert_callout_warning') }}</button></li>
                    <li><button type="button" data-action="insertCalloutDanger" title="{{ trans('entities.pages_md_insert_callout_danger') }}">{{ trans('entities.pages_md_insert_callout_danger') }}</button></li>
                </ul>
            </div>
            <div component="dropdown" class="buttons flex-container-row items-stretch">
                @if(config('services.drawio'))
                    <button class="text-button" type="button" data-action="insertDrawing" title="{{ trans('entities.pages_md_insert_drawing') }}">@icon('drawing')</button>
                @endif
                <button class="text-button" type="button" data-action="insertImage" title="{{ trans('entities.pages_md_insert_image') }}">@icon('image')</button>
                <button class="text-button" type="button" data-action="insertLink" title="{{ trans('entities.pages_md_insert_link') }}">@icon('link')</button>
                <button class="text-button" type="button" data-action="fullscreen" title="{{ trans('common.fullscreen') }}">@icon('fullscreen')</button>
                <button refs="dropdown@toggle" class="text-button" type="button" title="{{ trans('common.more') }}">@icon('more')</button>
                <div refs="dropdown@menu markdown-editor@setting-container" class="dropdown-menu" role="menu">
                    <div class="px-m">
                        @include('form.custom-checkbox', ['name' => 'md-showPreview', 'label' => trans('entities.pages_md_show_preview'), 'value' => true, 'checked' => true])
                    </div>
                    <hr class="m-none">
                    <div class="px-m">
                        @include('form.custom-checkbox', ['name' => 'md-scrollSync', 'label' => trans('entities.pages_md_sync_scroll'), 'value' => true, 'checked' => true])
                    </div>
                </div>
            </div>
        </div>

        <div markdown-input class="flex flex-fill">
            <textarea id="markdown-editor-input"
                      refs="markdown-editor@input"
                      @if($errors->has('markdown')) class="text-neg" @endif
                      name="markdown"
                      rows="5">@if(isset($model) || old('markdown')){{ old('markdown') ?? ($model->markdown === '' ? $model->html : $model->markdown) }}@endif</textarea>
        </div>

    </div>

    <div refs="markdown-editor@display-wrap" class="markdown-editor-wrap flex-container-row items-stretch" style="display: none">
        <div refs="markdown-editor@divider" class="markdown-panel-divider flex-fill"></div>
        <div class="flex-container-column flex flex-fill">
            <div class="editor-toolbar">
                <div class="editor-toolbar-label text-mono px-m py-xs">{{ trans('entities.pages_md_preview') }}</div>
            </div>
            <iframe src="about:blank"
                    refs="markdown-editor@display"
                    class="markdown-display flex flex-fill"
                    sandbox="allow-same-origin"></iframe>
        </div>
    </div>
</div>



@if($errors->has('markdown'))
    <div class="text-neg text-small">{{ $errors->first('markdown') }}</div>
@endif
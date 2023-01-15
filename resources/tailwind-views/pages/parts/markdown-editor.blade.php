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
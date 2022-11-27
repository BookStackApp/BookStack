<div id="markdown-editor" component="markdown-editor"
     option:markdown-editor:page-id="{{ $model->id ?? 0 }}"
     option:markdown-editor:text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     option:markdown-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:markdown-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-fill flex code-fill">

    <div class="markdown-editor-wrap active">
        <div class="editor-toolbar flex-container-row items-stretch justify-space-between">
            <div class="editor-toolbar-label text-mono px-m py-xs flex-container-row items-center flex">
                <span>{{ trans('entities.pages_md_editor') }}</span>
            </div>
            <div class="buttons flex-container-row items-stretch">
                @if(config('services.drawio'))
                    <button class="text-button" type="button" data-action="insertDrawing" title="{{ trans('entities.pages_md_insert_drawing') }}">@icon('drawing')</button>
                @endif
                <button class="text-button" type="button" data-action="insertImage" title="{{ trans('entities.pages_md_insert_image') }}">@icon('image')</button>
                <button class="text-button" type="button" data-action="insertLink" title="{{ trans('entities.pages_md_insert_link') }}">@icon('link')</button>
                <button class="text-button" type="button" data-action="fullscreen" title="{{ trans('common.fullscreen') }}">@icon('fullscreen')</button>
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

    <div class="markdown-editor-wrap">
        <div class="editor-toolbar">
            <div class="editor-toolbar-label text-mono px-m py-xs">{{ trans('entities.pages_md_preview') }}</div>
        </div>
        <iframe src="about:blank"
                refs="markdown-editor@display"
                class="markdown-display"
                sandbox="allow-same-origin"></iframe>
    </div>
</div>



@if($errors->has('markdown'))
    <div class="text-neg text-small">{{ $errors->first('markdown') }}</div>
@endif
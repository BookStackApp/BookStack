<div component="wysiwyg-editor"
     option:wysiwyg-editor:language="{{ $locale->htmlLang() }}"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ $locale->htmlDirection() }}"
     option:wysiwyg-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-container-column flex-fill flex">

    <div class="editor-container flex-container-column flex-fill flex" refs="wysiwyg-editor@edit-container">
    </div>

{{--    <div id="lexical-debug" style="white-space: pre-wrap; font-size: 12px; height: 200px; overflow-y: scroll; background-color: #000; padding: 1rem; border-radius: 4px; color: #FFF;"></div>--}}

    <textarea refs="wysiwyg-editor@input" id="html-editor" hidden="hidden"  name="html" rows="5">{{ old('html') ?? $model->html ?? '' }}</textarea>
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

@include('form.editor-translations')
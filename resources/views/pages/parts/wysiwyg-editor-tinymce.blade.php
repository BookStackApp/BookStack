@push('head')
    <script src="{{ versioned_asset('libs/tinymce/tinymce.min.js') }}" nonce="{{ $cspNonce }}"></script>
@endpush

<div component="wysiwyg-editor-tinymce"
     option:wysiwyg-editor-tinymce:language="{{ $locale->htmlLang() }}"
     option:wysiwyg-editor-tinymce:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor-tinymce:text-direction="{{ $locale->htmlDirection() }}"
     option:wysiwyg-editor-tinymce:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor-tinymce:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-fill flex">

    <textarea id="html-editor"  name="html" rows="5"
          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

@include('form.editor-translations')
<div component="wysiwyg-editor"
     option:wysiwyg-editor:language="{{ config('app.lang') }}"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     option:wysiwyg-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="flex-fill flex">

    <textarea id="html-editor"  name="html" rows="5"
          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

@include('pages.parts.editor-translations')
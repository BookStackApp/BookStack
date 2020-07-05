<div component="wysiwyg-editor"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
     class="flex-fill flex">

    @exposeTranslations([
        'errors.image_upload_error',
    ])

    <textarea id="html-editor"  name="html" rows="5" v-pre
          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif
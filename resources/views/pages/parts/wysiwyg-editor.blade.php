<div component="wysiwyg-editor"
     option:wysiwyg-editor:language="{{ $locale->htmlLang() }}"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ $locale->htmlDirection() }}"
     option:wysiwyg-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="">

    <style>
        .editor-toolbar-button-active {
            background-color: tomato;
        }
    </style>

    <div refs="wysiwyg-editor@edit-area" contenteditable="true">
        <p id="Content!">Some <strong>content</strong> here</p>
        <h2>List below this h2 header</h2>
        <ul>
            <li>Hello</li>
        </ul>

        <p class="callout danger">
            Hello there, this is an info callout
        </p>
    </div>

    <div id="lexical-debug" style="white-space: pre-wrap; font-size: 12px; height: 200px; overflow-y: scroll; background-color: #000; padding: 1rem; border-radius: 4px; color: #FFF;"></div>

{{--    <textarea id="html-editor"  name="html" rows="5"--}}
{{--          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>--}}
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

{{--TODO - @include('form.editor-translations')--}}
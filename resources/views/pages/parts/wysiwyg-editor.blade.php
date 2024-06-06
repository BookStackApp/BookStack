<div component="wysiwyg-editor"
     option:wysiwyg-editor:language="{{ $locale->htmlLang() }}"
     option:wysiwyg-editor:page-id="{{ $model->id ?? 0 }}"
     option:wysiwyg-editor:text-direction="{{ $locale->htmlDirection() }}"
     option:wysiwyg-editor:image-upload-error-text="{{ trans('errors.image_upload_error') }}"
     option:wysiwyg-editor:server-upload-limit-text="{{ trans('errors.server_upload_limit') }}"
     class="">

    <div class="editor-container">
        <div refs="wysiwyg-editor@edit-area" contenteditable="true">
            <p id="Content!">Some <strong>content</strong> here</p>
            <p>Content with image in, before text. <img src="https://bookstack.local/bookstack/uploads/images/gallery/2022-03/scaled-1680-/cats-image-2400x1000-2.jpg" width="200" alt="Sleepy meow"> After text.</p>
            <p>This has a <a href="https://example.com" target="_blank" title="Link to example">link</a> in it</p>
            <h2>List below this h2 header</h2>
            <ul>
                <li>Hello</li>
            </ul>

            <details>
                <summary>Collapsible details/summary block</summary>
                <p>Inner text here</p>
                <h4>Inner Header</h4>
                <p>More text <strong>with bold in</strong> it</p>
            </details>

            <p class="callout info">
                Hello there, this is an info callout
            </p>
        </div>
    </div>

    <div id="lexical-debug" style="white-space: pre-wrap; font-size: 12px; height: 200px; overflow-y: scroll; background-color: #000; padding: 1rem; border-radius: 4px; color: #FFF;"></div>

{{--    <textarea id="html-editor"  name="html" rows="5"--}}
{{--          @if($errors->has('html')) class="text-neg" @endif>@if(isset($model) || old('html')){{ old('html') ? old('html') : $model->html }}@endif</textarea>--}}
</div>

@if($errors->has('html'))
    <div class="text-neg text-small">{{ $errors->first('html') }}</div>
@endif

{{--TODO - @include('form.editor-translations')--}}
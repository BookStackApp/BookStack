<textarea component="wysiwyg-input"
          option:wysiwyg-input:text-direction="{{ $locale->htmlDirection() }}"
          id="description_html" name="description_html" rows="5"
          @if($errors->has('description_html')) class="text-neg" @endif>@if(isset($model) || old('description_html')){{ old('description_html') ?? $model->descriptionHtml()}}@endif</textarea>
@if($errors->has('description_html'))
    <div class="text-neg text-small">{{ $errors->first('description_html') }}</div>
@endif
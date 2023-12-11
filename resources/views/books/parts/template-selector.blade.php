<p class="text-muted small">
    {{ trans('entities.books_default_template_explain') }}
</p>

<select name="default_template" id="default_template">
    <option value="">---</option>
    @foreach ($templates as $template)
        <option @if(isset($entity) && $entity->default_template === $template->id) selected @endif value="{{ $template->id }}">{{ $template->name }}</option>
    @endforeach
</select>


@include('settings.parts.page-picker', ['name' => 'setting-app-homepage', 'placeholder' => trans('settings.app_homepage_select'), 'value' => setting('app-homepage')])
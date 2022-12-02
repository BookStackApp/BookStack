<p class="text-muted small">
    {!! nl2br(e(trans('entities.default_template_explain'))) !!}
</p>

<select name="default_template" id="default_template">
    <option value="">---</option>
    @foreach ($templates as $template)
        <option @if(isset($entity) && $entity->default_template === $template->id) selected @endif value="{{ $template->id }}">{{ $template->name }}</option>
    @endforeach
</select>
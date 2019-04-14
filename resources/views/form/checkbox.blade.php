{{--
$name
$label
$errors?
$model?
--}}
@include('components.custom-checkbox', [
    'name' => $name,
    'label' => $label,
    'value' => 'true',
    'checked' => old($name) || (!old() && isset($model) && $model->$name)
])

@if($errors->has($name))
    <div class="text-neg text-small">{{ $errors->first($name) }}</div>
@endif
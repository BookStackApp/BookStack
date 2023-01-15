{{--
$params - The query paramters to convert to inputs.
--}}
@foreach(array_intersect_key(request()->query(), array_flip($params)) as $name => $value)
    @if ($value)
    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endif
@endforeach
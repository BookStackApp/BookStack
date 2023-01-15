{{--
$filters - Array of search filter values
$name - Name of filter to limit use.
$value - Value of filter to use
--}}
<label class="checkbox">
    <input type="checkbox"
           name="filters[{{ $name }}]"
           @if (isset($filters[$name]) && (!$value || ($value && $value === $filters[$name]))) checked="checked" @endif
           value="{{ $value ?: 'true' }}">
    {{ $slot }}
</label>
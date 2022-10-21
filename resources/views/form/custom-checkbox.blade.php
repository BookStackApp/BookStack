{{--
$name
$value
$checked
$label
--}}
<label custom-checkbox class="toggle-switch @if($errors->has($name)) text-neg @endif">
    <input type="checkbox" name="{{$name}}" value="{{ $value }}" @if($checked) checked="checked" @endif @if($disabled ?? false) disabled="disabled" @endif>
    <span tabindex="0" role="checkbox"
          aria-checked="{{ $checked ? 'true' : 'false' }}"
          class="custom-checkbox text-primary">@icon('check')</span>
    <span class="label">{{$label}}</span>
</label>
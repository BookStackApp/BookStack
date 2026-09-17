{{--
$name - string
$value - string
$checked - boolean
$disabled - boolean
$label - string
$ariaLabel? (optional, falls back to $label)
--}}
<label component="custom-checkbox" id="custom-checkbox-label-{{ $name }}" class="toggle-switch @if($errors->has($name)) text-neg @endif">
    <input type="checkbox" name="{{$name}}" value="{{ $value }}" @if($checked) checked="checked" @endif @if($disabled ?? false) disabled="disabled" @endif>
    <span tabindex="0" role="checkbox"
          aria-checked="{{ $checked ? 'true' : 'false' }}"
          @if(isset($ariaLabel))
              aria-label="{{ $ariaLabel }}"
          @else
              aria-labelledby="custom-checkbox-label-{{ $name }}"
          @endif
          class="custom-checkbox text-primary">@icon('check')</span>
    <span class="label">{{$label}}</span>
</label>
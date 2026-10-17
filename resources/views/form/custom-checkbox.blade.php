{{--
$name - string
$value - string
$checked - boolean
$disabled - boolean
$label - string
$ariaLabel? (optional, intended for use if $label is non-visble/empty)
--}}
<label id="custom-checkbox-label-{{ $name }}" class="custom-checkbox @if($errors->has($name)) text-neg @endif">
    <input type="checkbox"
           name="{{ $name }}"
           value="{{ $value }}"
           @if($checked) checked="checked" @endif
           @if(isset($ariaLabel))
               aria-label="{{ $ariaLabel }}"
           @else
               aria-labelledby="custom-checkbox-label-{{ $name }}"
           @endif
           @if($disabled ?? false) disabled="disabled" @endif>
    <span class="label">{{ $label }}</span>
</label>
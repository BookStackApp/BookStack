<label components="custom-checkbox toggle-switch" class="toggle-switch">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <input type="checkbox" @if($value) checked="checked" @endif>
    <span tabindex="0" role="checkbox"
          aria-checked="{{ $value ? 'true' : 'false' }}"
          class="custom-checkbox text-primary">@icon('check')</span>
    <span class="label">{{ $label }}</span>
</label>
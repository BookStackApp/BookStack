<label components="custom-checkbox toggle-switch" id="toggle-switch-label-{{ $name }}" class="toggle-switch">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <input type="checkbox" @if($value) checked="checked" @endif>
    <span tabindex="0"
          role="checkbox"
          aria-labelledby="toggle-switch-label-{{ $name }}"
          aria-checked="{{ $value ? 'true' : 'false' }}"
          class="custom-checkbox text-primary">@icon('check')</span>
    <span class="label">{{ $label }}</span>
</label>
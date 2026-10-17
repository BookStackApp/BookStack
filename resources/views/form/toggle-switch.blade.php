<label component="toggle-switch" id="toggle-switch-label-{{ $name }}" class="custom-checkbox">
    <input type="hidden" name="{{ $name }}" value="{{ $value ? 'true' : 'false' }}"/>
    <input type="checkbox"
           aria-labelledby="toggle-switch-label-{{ $name }}"
           @if($value) checked="checked" @endif>
    <span class="label">{{ $label }}</span>
</label>
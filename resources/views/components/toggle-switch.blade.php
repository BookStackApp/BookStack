<label toggle-switch="{{$name}}" custom-checkbox class="toggle-switch">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <input type="checkbox" @if($value) checked="checked" @endif>
    <span role="checkbox"
          aria-checked="{{ $value ? 'true' : 'false' }}"
          class="custom-checkbox text-primary">@icon('check')</span>
    <span class="label">{{ $label }}</span>
</label>
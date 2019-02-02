<label toggle-switch="{{$name}}" class="toggle-switch">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <input type="checkbox" @if($value) checked="checked" @endif>
    <span class="custom-checkbox ">@icon('check')</span>
    <span class="label">{{ $label ?? '' }}</span> {{-- TODO - remove default operataor backup --}}
</label>
<div toggle-switch="{{$name}}" class="toggle-switch @if($value) active @endif">
    <input type="hidden" name="{{$name}}" value="{{$value?'true':'false'}}"/>
    <div class="switch-handle"></div>
</div>
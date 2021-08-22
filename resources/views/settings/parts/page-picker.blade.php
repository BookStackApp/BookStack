
{{--Depends on entity selector popup--}}
<div page-picker>
    <div class="input-base">
        <span @if($value) style="display: none" @endif page-picker-default class="text-muted italic">{{ $placeholder }}</span>
        <a @if(!$value) style="display: none" @endif href="{{ url('/link/' . $value) }}" target="_blank" rel="noopener" class="text-page" page-picker-display>#{{$value}}, {{$value ? \BookStack\Entities\Models\Page::find($value)->name : '' }}</a>
    </div>
    <br>
    <input type="hidden" value="{{$value}}" name="{{$name}}" id="{{$name}}">
    <button @if(!$value) style="display: none" @endif type="button" page-picker-reset class="text-button">{{ trans('common.reset') }}</button>
    <span @if(!$value) style="display: none" @endif class="sep">|</span>
    <button type="button" page-picker-select class="text-button">{{ trans('common.select') }}</button>
</div>
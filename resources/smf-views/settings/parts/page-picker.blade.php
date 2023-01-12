
{{--Depends on entity selector popup--}}
<div component="page-picker">
    <div class="input-base">
        <span @if($value) style="display: none" @endif refs="page-picker@default-display" class="text-muted italic">{{ $placeholder }}</span>
        <a @if(!$value) style="display: none" @endif href="{{ url('/link/' . $value) }}" target="_blank" rel="noopener" class="text-page" refs="page-picker@display">#{{$value}}, {{$value ? \BookStack\Entities\Models\Page::find($value)->name : '' }}</a>
    </div>
    <br>
    <input refs="page-picker@input" type="hidden" value="{{$value}}" name="{{$name}}" id="{{$name}}">
    <button @if(!$value) style="display: none" @endif type="button" refs="page-picker@reset-button" class="text-button">{{ trans('common.reset') }}</button>
    <span refs="page-picker@button-seperator" @if(!$value) style="display: none" @endif class="sep">|</span>
    <button type="button" refs="page-picker@select-button" class="text-button">{{ trans('common.select') }}</button>
</div>
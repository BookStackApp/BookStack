
{{--Depends on entity selector popup--}}
<div component="page-picker"
     option:page-picker:selector-endpoint="{{ $selectorEndpoint }}">
    <div class="input-base overflow-hidden height-auto">
        <span @if($value) hidden @endif refs="page-picker@default-display" class="text-muted italic">{{ $placeholder }}</span>
        <a @if(!$value) hidden @endif href="{{ url('/link/' . $value) }}" target="_blank" rel="noopener" class="text-page" refs="page-picker@display">#{{$value}}, {{$value ? \BookStack\Entities\Models\Page::query()->visible()->find($value)->name ?? '' : '' }}</a>
    </div>
    <br>
    <input refs="page-picker@input" type="hidden" value="{{$value}}" name="{{$name}}" id="{{$name}}">
    <button @if(!$value) hidden @endif type="button" refs="page-picker@reset-button" class="text-button">{{ trans('common.reset') }}</button>
    <span refs="page-picker@button-seperator" @if(!$value) hidden @endif class="sep">|</span>
    <button type="button" refs="page-picker@select-button" class="text-button">{{ trans('common.select') }}</button>
</div>
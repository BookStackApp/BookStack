<div class="image-picker" image-picker="{{$name}}" data-default-image="{{ $defaultImage }}" data-resize-height="{{ $resizeHeight }}" data-resize-width="{{ $resizeWidth }}" data-current-id="{{ $currentId or '' }}" data-resize-crop="{{ $resizeCrop or '' }}">

    <div>
        <img @if($currentImage && $currentImage !== 'none') src="{{$currentImage}}" @else src="{{$defaultImage}}" @endif  class="{{$imageClass}} @if($currentImage=== 'none') none @endif" alt="{{ trans('components.image_preview') }}">
    </div>

    <button class="button" type="button" data-action="show-image-manager">{{ trans('components.image_select_image') }}</button>
    <br>
    <button class="text-button" data-action="reset-image" type="button">{{ trans('common.reset') }}</button>

    @if ($showRemove)
        <span class="sep">|</span>
        <button class="text-button neg" data-action="remove-image" type="button">{{ trans('common.remove') }}</button>
    @endif

    <input type="hidden" name="{{$name}}" id="{{$name}}" value="{{ isset($currentId) && ($currentId !== 0 && $currentId !== false) ? $currentId : $currentImage}}">
</div>
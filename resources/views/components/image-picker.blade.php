<div class="image-picker" image-picker="{{$name}}" data-default-image="{{ $defaultImage }}" data-resize-height="{{ $resizeHeight }}" data-resize-width="{{ $resizeWidth }}" data-current-id="{{ $currentId ?? '' }}" data-resize-crop="{{ $resizeCrop ?? '' }}">

    <div class="grid half">
        <div class="text-center">
            <img @if($currentImage && $currentImage !== 'none') src="{{$currentImage}}" @else src="{{$defaultImage}}" @endif  class="{{$imageClass}} @if($currentImage=== 'none') none @endif" alt="{{ trans('components.image_preview') }}">
        </div>
        <div class="text-center">
            <button class="button outline small" type="button" data-action="show-image-manager">{{ trans('components.image_select_image') }}</button>
            <br>
            <button class="text-button text-muted" data-action="reset-image" type="button">{{ trans('common.reset') }}</button>

            @if ($showRemove)
                <span class="sep">|</span>
                <button class="text-button text-muted" data-action="remove-image" type="button">{{ trans('common.remove') }}</button>
            @endif
        </div>
    </div>

    <input type="hidden" name="{{$name}}" id="{{$name}}" value="{{ isset($currentId) && ($currentId !== 0 && $currentId !== false) ? $currentId : $currentImage}}">
{{--    TODO - Revamp to be custom file upload button, instead of being linked to image manager--}}
{{--    TODO - Remove image manager use where this is used and clean image manager for drawing/gallery use.--}}
</div>
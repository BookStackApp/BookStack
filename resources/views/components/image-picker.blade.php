<div class="image-picker @if($errors->has($name)) has-error @endif"
     image-picker="{{$name}}"
     data-default-image="{{ $defaultImage }}">

    <div class="grid half">
        <div class="text-center">
            <img @if($currentImage && $currentImage !== 'none') src="{{$currentImage}}" @else src="{{$defaultImage}}" @endif  class="{{$imageClass}} @if($currentImage=== 'none') none @endif" alt="{{ trans('components.image_preview') }}">
        </div>
        <div class="text-center">

            <input type="file" class="custom-file-input" accept="image/*" name="{{ $name }}" id="{{ $name }}">
            <label for="{{ $name }}" class="button outline">{{ trans('components.image_select_image') }}</label>
            <input type="hidden" data-reset-input name="{{ $name }}_reset" value="true" disabled="disabled">
            @if(isset($removeName))
                <input type="hidden" data-remove-input name="{{ $removeName }}" value="{{ $removeValue }}" disabled="disabled">
            @endif

            <br>
            <button class="text-button text-muted" data-action="reset-image" type="button">{{ trans('common.reset') }}</button>

            @if(isset($removeName))
                <span class="sep">|</span>
                <button class="text-button text-muted" data-action="remove-image" type="button">{{ trans('common.remove') }}</button>
            @endif
        </div>
    </div>

    @if($errors->has($name))
        <div class="text-neg text-small">{{ $errors->first($name) }}</div>
    @endif

</div>
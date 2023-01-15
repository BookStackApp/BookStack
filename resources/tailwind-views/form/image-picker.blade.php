<div component="image-picker"
     option:image-picker:default-image="{{ $defaultImage }}"
     class="image-picker @if($errors->has($name)) has-error @endif">

    <div class="grid half">
        <div class="text-center">
            <img refs="image-picker@image"
                @if($currentImage && $currentImage !== 'none') src="{{$currentImage}}" @else src="{{$defaultImage}}" @endif
                class="{{$imageClass}} @if($currentImage=== 'none') none @endif" alt="{{ trans('components.image_preview') }}">
        </div>
        <div class="text-center">
            <input refs="image-picker@image-input" type="file" class="custom-file-input" accept="image/*" name="{{ $name }}" id="{{ $name }}">
            <label for="{{ $name }}" class="button outline">{{ trans('components.image_select_image') }}</label>
            <input refs="image-picker@reset-input" type="hidden" name="{{ $name }}_reset" value="true" disabled="disabled">
            @if(isset($removeName))
                <input refs="image-picker@remove-input" type="hidden" name="{{ $removeName }}" value="{{ $removeValue }}" disabled="disabled">
            @endif

            <br>
            <button refs="image-picker@reset-button" class="text-button text-muted" type="button">{{ trans('common.reset') }}</button>

            @if(isset($removeName))
                <span class="sep">|</span>
                <button refs="image-picker@remove-button" class="text-button text-muted" type="button">{{ trans('common.remove') }}</button>
            @endif
        </div>
    </div>

    @if($errors->has($name))
        <div class="text-neg text-small">{{ $errors->first($name) }}</div>
    @endif

</div>
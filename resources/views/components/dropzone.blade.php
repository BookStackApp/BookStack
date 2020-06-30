{{--
@url - URL to upload to.
@placeholder - Placeholder text
--}}
<div component="dropzone"
     option:dropzone:url="{{ $url }}"
     class="dropzone-container text-center">
    <button type="button" class="dz-message">{{ $placeholder }}</button>
</div>
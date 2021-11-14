{{--
@url - URL to upload to.
@placeholder - Placeholder text
@successMessage
--}}
<div component="dropzone"
     option:dropzone:url="{{ $url }}"
     option:dropzone:success-message="{{ $successMessage ?? '' }}"
     option:dropzone:remove-message="{{ trans('components.image_upload_remove') }}"
     option:dropzone:upload-limit="{{ config('app.upload_limit') }}"
     option:dropzone:upload-limit-message="{{ trans('errors.server_upload_limit') }}"
     option:dropzone:timeout-message="{{ trans('errors.file_upload_timeout') }}"

     class="dropzone-container text-center">
    <button type="button" class="dz-message">{{ $placeholder }}</button>
</div>
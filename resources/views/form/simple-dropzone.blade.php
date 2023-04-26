{{--
@url - URL to upload to.
@placeholder - Placeholder text
@successMessage
--}}
<div component="dropzone"
     option:dropzone:url="{{ $url }}"
     option:dropzone:success-message="{{ $successMessage }}"
     option:dropzone:error-message="{{ trans('errors.attachment_upload_error') }}"
     option:dropzone:upload-limit="{{ config('app.upload_limit') }}"
     option:dropzone:upload-limit-message="{{ trans('errors.server_upload_limit') }}"
     option:dropzone:zone-text="{{ trans('entities.attachments_dropzone') }}"
     option:dropzone:file-accept="*"
     class="relative">
    <div refs="dropzone@status-area" class="fixed top-right px-m py-m"></div>
    <button type="button"
            refs="dropzone@select-button dropzone@drop-target"
            class="dropzone-landing-area text-center">
        {{ $placeholder }}
    </button>
</div>
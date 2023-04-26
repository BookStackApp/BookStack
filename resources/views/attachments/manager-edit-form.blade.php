<div component="ajax-form"
     option:ajax-form:url="/attachments/{{ $attachment->id }}"
     option:ajax-form:method="put"
     option:ajax-form:response-container=".attachment-edit-container"
     option:ajax-form:success-message="{{ trans('entities.attachments_updated_success') }}">
    <h5>{{ trans('entities.attachments_edit_file') }}</h5>

    <div class="form-group">
        <label for="attachment_edit_name">{{ trans('entities.attachments_edit_file_name') }}</label>
        <input type="text" id="attachment_edit_name"
               name="attachment_edit_name"
               value="{{ $attachment_edit_name ?? $attachment->name ?? '' }}"
               placeholder="{{ trans('entities.attachments_edit_file_name') }}">
        @if($errors->has('attachment_edit_name'))
            <div class="text-neg text-small">{{ $errors->first('attachment_edit_name') }}</div>
        @endif
    </div>

    <div component="tabs" class="tab-container">
        <div class="nav-tabs" role="tablist">
            <button id="attachment-edit-file-tab"
                    type="button"
                    aria-controls="attachment-edit-file-panel"
                    aria-selected="{{ $attachment->external ? 'false' : 'true' }}"
                    role="tab">{{ trans('entities.attachments_upload') }}</button>
            <button id="attachment-edit-link-tab"
                    type="button"
                    aria-controls="attachment-edit-link-panel"
                    aria-selected="{{ $attachment->external ? 'true' : 'false' }}"
                    role="tab">{{ trans('entities.attachments_set_link') }}</button>
        </div>
        <div id="attachment-edit-file-panel"
             @if($attachment->external) hidden @endif
             tabindex="0"
             role="tabpanel"
             aria-labelledby="attachment-edit-file-tab"
             class="mb-m">
            @include('form.simple-dropzone', [
                'placeholder' => trans('entities.attachments_edit_drop_upload'),
                'url' =>  url('/attachments/upload/' . $attachment->id),
                'successMessage' => trans('entities.attachments_file_updated'),
            ])
        </div>
        <div id="attachment-edit-link-panel"
             @if(!$attachment->external) hidden @endif
             tabindex="0"
             role="tabpanel"
             aria-labelledby="attachment-edit-link-tab">
            <div class="form-group">
                <label for="attachment_edit_url">{{ trans('entities.attachments_link_url') }}</label>
                <input type="text" id="attachment_edit_url"
                       name="attachment_edit_url"
                       value="{{ $attachment_edit_url ?? ($attachment->external ? $attachment->path : '')  }}"
                       placeholder="{{ trans('entities.attachment_link') }}">
                @if($errors->has('attachment_edit_url'))
                    <div class="text-neg text-small">{{ $errors->first('attachment_edit_url') }}</div>
                @endif
            </div>
        </div>
    </div>

    <button component="event-emit-select"
            option:event-emit-select:name="edit-back"
            type="button"
            class="button outline">{{ trans('common.back') }}</button>
    <button refs="ajax-form@submit" type="button" class="button">{{ trans('common.save') }}</button>
</div>
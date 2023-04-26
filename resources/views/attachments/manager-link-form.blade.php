{{--
@pageId
--}}
<div component="ajax-form"
     option:ajax-form:url="/attachments/link"
     option:ajax-form:method="post"
     option:ajax-form:response-container=".link-form-container"
     option:ajax-form:success-message="{{ trans('entities.attachments_link_attached') }}">
    <input type="hidden" name="attachment_link_uploaded_to" value="{{ $pageId }}">
    <p class="text-muted small">{{ trans('entities.attachments_explain_link') }}</p>
    <div class="form-group">
        <label for="attachment_link_name">{{ trans('entities.attachments_link_name') }}</label>
        <input name="attachment_link_name" id="attachment_link_name" type="text" placeholder="{{ trans('entities.attachments_link_name') }}" value="{{ $attachment_link_name ?? '' }}">
        @if($errors->has('attachment_link_name'))
            <div class="text-neg text-small">{{ $errors->first('attachment_link_name') }}</div>
        @endif
    </div>
    <div class="form-group">
        <label for="attachment_link_url">{{ trans('entities.attachments_link_url') }}</label>
        <input name="attachment_link_url" id="attachment_link_url" type="text" placeholder="{{ trans('entities.attachments_link_url_hint') }}" value="{{ $attachment_link_url ?? '' }}">
        @if($errors->has('attachment_link_url'))
            <div class="text-neg text-small">{{ $errors->first('attachment_link_url') }}</div>
        @endif
    </div>
    <button component="event-emit-select"
            option:event-emit-select:name="edit-back"
            type="button" class="button outline">{{ trans('common.cancel') }}</button>
    <button refs="ajax-form@submit"
            type="button"
            class="button">{{ trans('entities.attach') }}</button>
</div>
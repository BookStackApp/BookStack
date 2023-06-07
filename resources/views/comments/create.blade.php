<div refs="page-comments@form-container" hidden class="comment-box">

    <div class="header p-s">{{ trans('entities.comment_new') }}</div>
    <div refs="page-comments@reply-to-row" hidden class="primary-background-light text-muted px-s py-xs mb-s">
        <div class="grid left-focus v-center">
            <div>
                {!! trans('entities.comment_in_reply_to', ['commentId' => '<a href=""></a>']) !!}
            </div>
            <div class="text-right">
                <button refs="page-comments@remove-reply-to-button" class="text-button">{{ trans('common.remove') }}</button>
            </div>
        </div>
    </div>

    <div class="content px-s">
        <form refs="page-comments@form" novalidate>
            <div class="form-group description-input">
                <textarea refs="page-comments@form-input" name="markdown"
                          rows="3"
                          placeholder="{{ trans('entities.comment_placeholder') }}"></textarea>
            </div>
            <div class="form-group text-right">
                <button type="button" class="button outline"
                        refs="page-comments@hide-form-button">{{ trans('common.cancel') }}</button>
                <button type="submit" class="button">{{ trans('entities.comment_save') }}</button>
            </div>
        </form>
    </div>

</div>
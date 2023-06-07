<div component="page-comment"
     option:page-comment:comment-id="{{ $comment->id }}"
     option:page-comment:comment-local-id="{{ $comment->local_id }}"
     option:page-comment:comment-parent-id="{{ $comment->parent_id }}"
     option:page-comment:updated-text="{{ trans('entities.comment_updated_success') }}"
     option:page-comment:deleted-text="{{ trans('entities.comment_deleted_success') }}"
     id="comment{{$comment->local_id}}"
     class="comment-box">
    <div class="header p-s">
        <div class="grid half left-focus no-gap v-center">
            <div class="meta text-muted text-small">
                <a href="#comment{{$comment->local_id}}">#{{$comment->local_id}}</a>
                &nbsp;&nbsp;
                @if ($comment->createdBy)
                    <img width="50" src="{{ $comment->createdBy->getAvatar(50) }}" class="avatar" alt="{{ $comment->createdBy->name }}">
                    &nbsp;
                    <a href="{{ $comment->createdBy->getProfileUrl() }}">{{ $comment->createdBy->name }}</a>
                @else
                    <span>{{ trans('common.deleted_user') }}</span>
                @endif
                <span title="{{ $comment->created_at }}">{{ trans('entities.comment_created', ['createDiff' => $comment->created]) }}</span>
                @if($comment->isUpdated())
                    <span title="{{ $comment->updated_at }}">
                &bull;&nbsp;
                    {{ trans('entities.comment_updated', ['updateDiff' => $comment->updated, 'username' => $comment->updatedBy? $comment->updatedBy->name : trans('common.deleted_user')]) }}
            </span>
                @endif
            </div>
            <div class="actions text-right">
                @if(userCan('comment-update', $comment))
                    <button refs="page-comment@edit-button" type="button" class="text-button"  aria-label="{{ trans('common.edit') }}" title="{{ trans('common.edit') }}">@icon('edit')</button>
                @endif
                @if(userCan('comment-create-all'))
                    <button refs="page-comment@reply-button" type="button" class="text-button" aria-label="{{ trans('common.reply') }}" title="{{ trans('common.reply') }}">@icon('reply')</button>
                @endif
                @if(userCan('comment-delete', $comment))
                    <div component="dropdown" class="dropdown-container">
                        <button type="button" refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" class="text-button" title="{{ trans('common.delete') }}">@icon('delete')</button>
                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                            <li class="px-m text-small text-muted pb-s">{{trans('entities.comment_delete_confirm')}}</li>
                            <li>
                                <button refs="page-comment@delete-button" type="button" class="text-button text-neg icon-item">
                                    @icon('delete')
                                    <div>{{ trans('common.delete') }}</div>
                                </button>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>

    </div>

    @if ($comment->parent_id)
        <div class="reply-row primary-background-light text-muted px-s py-xs mb-s">
            {!! trans('entities.comment_in_reply_to', ['commentId' => '<a href="#comment'.$comment->parent_id.'">#'.$comment->parent_id.'</a>']) !!}
        </div>
    @endif

    <div refs="page-comment@content-container" class="content px-s pb-s">
        {!! $comment->html  !!}
    </div>

    @if(userCan('comment-update', $comment))
        <form novalidate refs="page-comment@form" hidden class="content px-s block">
            <div class="form-group description-input">
                <textarea refs="page-comment@input" name="markdown" rows="3" placeholder="{{ trans('entities.comment_placeholder') }}">{{ $comment->text }}</textarea>
            </div>
            <div class="form-group text-right">
                <button type="button" class="button outline" refs="page-comment@form-cancel">{{ trans('common.cancel') }}</button>
                <button type="submit" class="button">{{ trans('entities.comment_save') }}</button>
            </div>
        </form>
    @endif

</div>
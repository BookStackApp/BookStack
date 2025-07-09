@php
    $commentHtml = $comment->safeHtml();
@endphp
<div component="{{ $readOnly ? '' : 'page-comment' }}"
     option:page-comment:comment-id="{{ $comment->id }}"
     option:page-comment:comment-local-id="{{ $comment->local_id }}"
     option:page-comment:updated-text="{{ trans('entities.comment_updated_success') }}"
     option:page-comment:deleted-text="{{ trans('entities.comment_deleted_success') }}"
     option:page-comment:archive-text="{{ $comment->archived ? trans('entities.comment_unarchive_success') : trans('entities.comment_archive_success') }}"
     option:page-comment:wysiwyg-text-direction="{{ $locale->htmlDirection() }}"
     id="comment{{$comment->local_id}}"
     class="comment-box">
    <div class="header">
        <div class="flex-container-row wrap items-center gap-x-xs">
            @if ($comment->createdBy)
                <div>
                    <img width="50" src="{{ $comment->createdBy->getAvatar(50) }}" class="avatar block mr-xs" alt="{{ $comment->createdBy->name }}">
                </div>
            @endif
            <div class="meta text-muted flex-container-row wrap items-center flex text-small">
                @if ($comment->createdBy)
                    <a href="{{ $comment->createdBy->getProfileUrl() }}">{{ $comment->createdBy->getShortName(16) }}</a>
                @else
                    {{ trans('common.deleted_user') }}
                @endif
                <span title="{{ $comment->created_at }}">&nbsp;{{ trans('entities.comment_created', ['createDiff' => $comment->created_at->diffForHumans() ]) }}</span>
                @if($comment->isUpdated())
                    <span class="mx-xs">&bull;</span>
                    <span title="{{ trans('entities.comment_updated', ['updateDiff' => $comment->updated_at, 'username' => $comment->updatedBy->name ?? trans('common.deleted_user')]) }}">
                 {{ trans('entities.comment_updated_indicator') }}
                    </span>
                @endif
            </div>
            <div class="right-meta flex-container-row justify-flex-end items-center px-s">
                @if(!$readOnly && (userCan('comment-create-all') || userCan('comment-update', $comment) || userCan('comment-delete', $comment)))
                <div class="actions mr-s">
                    @if(userCan('comment-create-all'))
                        <button refs="page-comment@reply-button" type="button" class="text-button text-muted hover-underline text-small p-xs">@icon('reply') {{ trans('common.reply') }}</button>
                    @endif
                    @if(!$comment->parent_id && (userCan('comment-update', $comment) || userCan('comment-delete', $comment)))
                        <button refs="page-comment@archive-button"
                                type="button"
                                data-is-archived="{{ $comment->archived ? 'true' : 'false' }}"
                                class="text-button text-muted hover-underline text-small p-xs">@icon('archive') {{ trans('common.' . ($comment->archived ? 'unarchive' : 'archive')) }}</button>
                    @endif
                    @if(userCan('comment-update', $comment))
                        <button refs="page-comment@edit-button" type="button" class="text-button text-muted hover-underline text-small p-xs">@icon('edit') {{ trans('common.edit') }}</button>
                    @endif
                    @if(userCan('comment-delete', $comment))
                        <div component="dropdown" class="dropdown-container">
                            <button type="button" refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" class="text-button text-muted hover-underline text-small p-xs">@icon('delete') {{ trans('common.delete') }}</button>
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
                    <span class="text-muted">
                        &nbsp;&bull;&nbsp;
                    </span>
                </div>
                @endif
                <div>
                    <a class="bold text-muted text-small" href="#comment{{$comment->local_id}}">#{{$comment->local_id}}</a>
                </div>
            </div>
        </div>

    </div>

    <div refs="page-comment@content-container" class="content">
        @if ($comment->parent_id)
            <p class="comment-reply">
                <a class="text-muted text-small" href="#comment{{ $comment->parent_id }}">@icon('reply'){{ trans('entities.comment_in_reply_to', ['commentId' => '#' . $comment->parent_id]) }}</a>
            </p>
        @endif
        @if($comment->content_ref)
            <div class="comment-reference-indicator-wrap">
                <a component="page-comment-reference"
                   option:page-comment-reference:reference="{{ $comment->content_ref }}"
                   option:page-comment-reference:view-comment-text="{{ trans('entities.comment_view') }}"
                   option:page-comment-reference:jump-to-thread-text="{{ trans('entities.comment_jump_to_thread') }}"
                   option:page-comment-reference:close-text="{{ trans('common.close') }}"
                   href="#">@icon('bookmark'){{ trans('entities.comment_reference') }} <span>{{ trans('entities.comment_reference_outdated') }}</span></a>
            </div>
        @endif
        {!! $commentHtml  !!}
    </div>

    @if(!$readOnly && userCan('comment-update', $comment))
        <form novalidate refs="page-comment@form" hidden class="content pt-s px-s block">
            <div class="form-group description-input">
                <textarea refs="page-comment@input" name="html" rows="3" placeholder="{{ trans('entities.comment_placeholder') }}">{{ $commentHtml }}</textarea>
            </div>
            <div class="form-group text-right">
                <button type="button" class="button outline" refs="page-comment@form-cancel">{{ trans('common.cancel') }}</button>
                <button type="submit" class="button">{{ trans('entities.comment_save') }}</button>
            </div>
        </form>
    @endif

</div>
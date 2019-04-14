<div class="comment-box mb-m" comment="{{ $comment->id }}" local-id="{{$comment->local_id}}" parent-id="{{$comment->parent_id}}" id="comment{{$comment->local_id}}">
    <div class="header p-s">
        <div class="grid half no-gap v-center">
            <div class="meta">
                <a href="#comment{{$comment->local_id}}" class="text-muted">#{{$comment->local_id}}</a>
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
                    <button type="button" class="text-button" action="edit" title="{{ trans('common.edit') }}">@icon('edit')</button>
                @endif
                @if(userCan('comment-create-all'))
                    <button type="button" class="text-button" action="reply" title="{{ trans('common.reply') }}">@icon('reply')</button>
                @endif
                @if(userCan('comment-delete', $comment))
                    <div dropdown class="dropdown-container">
                        <button type="button" dropdown-toggle class="text-button" title="{{ trans('common.delete') }}">@icon('delete')</button>
                        <ul>
                            <li class="px-m text-small text-muted pb-s">{{trans('entities.comment_delete_confirm')}}</li>
                            <li><a action="delete" class="text-button text-neg" >@icon('delete'){{ trans('common.delete') }}</a></li>
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

    <div comment-content class="content px-s pb-s">
        <div class="form-group loading" style="display: none;">
            @include('partials.loading-icon', ['text' => trans('entities.comment_deleting')])
        </div>
        {!! $comment->html  !!}
    </div>

    @if(userCan('comment-update', $comment))
        <div comment-edit-container style="display: none;" class="content px-s">
            <form novalidate>
                <div class="form-group description-input">
                    <textarea name="markdown" rows="3" placeholder="{{ trans('entities.comment_placeholder') }}">{{ $comment->text }}</textarea>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="button outline" action="closeUpdateForm">{{ trans('common.cancel') }}</button>
                    <button type="submit" class="button primary">{{ trans('entities.comment_save') }}</button>
                </div>
                <div class="form-group loading" style="display: none;">
                    @include('partials.loading-icon', ['text' => trans('entities.comment_saving')])
                </div>
            </form>
        </div>
    @endif

</div>
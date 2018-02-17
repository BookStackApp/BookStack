<div class="comment-box" comment="{{ $comment->id }}" local-id="{{$comment->local_id}}" parent-id="{{$comment->parent_id}}" id="comment{{$comment->local_id}}">
    <div class="header">

        <div class="float right actions">
            @if(userCan('comment-update', $comment))
                <button type="button" class="text-button" action="edit" title="{{ trans('common.edit') }}">@icon('edit')</button>
            @endif
            @if(userCan('comment-create-all'))
                <button type="button" class="text-button" action="reply" title="{{ trans('common.reply') }}"><i class="zmdi zmdi-mail-reply-all"></i></button>
            @endif
            @if(userCan('comment-delete', $comment))

                <div dropdown class="dropdown-container">
                    <button type="button" dropdown-toggle class="text-button" title="{{ trans('common.delete') }}">@icon('delete')</button>
                    <ul>
                        <li class="padded"><small class="text-muted">{{trans('entities.comment_delete_confirm')}}</small></li>
                        <li><a action="delete" class="text-button neg" >@icon('delete'){{ trans('common.delete') }}</a></li>
                    </ul>
                </div>
            @endif
        </div>

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
            <span title="{{ $comment->created_at }}">
            {{ trans('entities.comment_created', ['createDiff' => $comment->created]) }}
        </span>
            @if($comment->isUpdated())
                <span title="{{ $comment->updated_at }}">
                &bull;&nbsp;
                    {{ trans('entities.comment_updated', ['updateDiff' => $comment->updated, 'username' => $comment->updatedBy? $comment->updatedBy->name : trans('common.deleted_user')]) }}
            </span>
            @endif
        </div>

    </div>

    @if ($comment->parent_id)
        <div class="reply-row primary-background-light text-muted">
            {!! trans('entities.comment_in_reply_to', ['commentId' => '<a href="#comment'.$comment->parent_id.'">#'.$comment->parent_id.'</a>']) !!}
        </div>
    @endif

    <div comment-content class="content">
        <div class="form-group loading" style="display: none;">
            @include('partials.loading-icon', ['text' => trans('entities.comment_deleting')])
        </div>
        {!! $comment->html  !!}
    </div>

    @if(userCan('comment-update', $comment))
        <div comment-edit-container style="display: none;" class="content">
            <form novalidate>
                <div class="form-group">
                    <textarea name="markdown" rows="3" v-model="comment.text" placeholder="{{ trans('entities.comment_placeholder') }}">{{ $comment->text }}</textarea>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="button outline" action="closeUpdateForm">{{ trans('common.cancel') }}</button>
                    <button type="submit" class="button pos">{{ trans('entities.comment_save') }}</button>
                </div>
                <div class="form-group loading" style="display: none;">
                    @include('partials.loading-icon', ['text' => trans('entities.comment_saving')])
                </div>
            </form>
        </div>
    @endif

</div>
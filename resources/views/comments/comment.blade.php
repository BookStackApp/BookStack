<div class="comment-box" comment="{{ $comment->id }}" id="comment{{$comment->local_id}}">
    <div class="header">

        <div class="float right actions">
            @if(userCan('comment-update', $comment))
                <button type="button" class="text-button" action="edit" title="{{ trans('common.edit') }}"><i class="zmdi zmdi-edit"></i></button>
            @endif
            @if(userCan('comment-create-all'))
                <button type="button" class="text-button" action="reply" title="{{ trans('common.reply') }}"><i class="zmdi zmdi-mail-reply-all"></i></button>
            @endif
            @if(userCan('comment-delete', $comment))
                <button type="button" class="text-button" action="delete" title="{{ trans('common.delete') }}"><i class="zmdi zmdi-delete"></i></button>
            @endif
        </div>

        <a href="#comment{{$comment->local_id}}" class="text-muted">#{{$comment->local_id}}</a>
        &nbsp;&nbsp;
        <img width="50" src="{{ $comment->createdBy->getAvatar(50) }}" class="avatar" alt="{{ $comment->createdBy->name }}">
        &nbsp;
        <a href="{{ $comment->createdBy->getProfileUrl() }}">{{ $comment->createdBy->name }}</a>
        {{--TODO - Account for deleted user--}}
        <span title="{{ $comment->created_at }}">
            {{ trans('entities.comment_created', ['createDiff' => $comment->created]) }}
        </span>
        @if($comment->isUpdated())
            <span title="{{ $comment->updated_at }}">
                &bull;&nbsp;
               {{ trans('entities.comment_updated', ['updateDiff' => $comment->updated, 'username' => $comment->updatedBy->name]) }}
            </span>
        @endif
    </div>
    <div comment-content class="content">
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
            </form>
        </div>
    @endif

</div>
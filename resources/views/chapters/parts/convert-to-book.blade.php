<div class="content-wrap card auto-height">
    <h2 class="list-heading">{{ trans('entities.convert_to_book') }}</h2>
    <div class="grid half left-focus no-row-gap">
        <p>
            {{ trans('entities.convert_to_book_desc') }}
        </p>
        <div class="text-m-right">
            <div component="dropdown" class="dropdown-container">
                <button refs="dropdown@toggle" class="button outline" aria-haspopup="true" aria-expanded="false">
                    {{ trans('entities.convert_chapter') }}
                </button>
                <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                    <li class="px-m py-s text-small text-muted">
                        {{ trans('entities.convert_chapter_confirm') }}
                        <br>
                        {{ trans('entities.convert_undo_warning') }}
                    </li>
                    <li>
                        <form action="{{ $chapter->getUrl('/convert-to-book') }}" method="POST">
                            {!! csrf_field() !!}
                            <button type="submit" class="text-link text-item">{{ trans('common.confirm') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
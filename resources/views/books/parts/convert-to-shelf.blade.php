<div class="content-wrap card auto-height">
    <h2 class="list-heading">{{ trans('entities.convert_to_shelf') }}</h2>
    <p>
        {{ trans('entities.convert_to_shelf_contents_desc') }}
        <br><br>
        {{ trans('entities.convert_to_shelf_permissions_desc') }}
    </p>
    <div class="text-right">
        <div component="dropdown" class="dropdown-container">
            <button refs="dropdown@toggle" class="button outline" aria-haspopup="true" aria-expanded="false">{{ trans('entities.convert_book') }}</button>
            <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                <li class="px-m py-s text-small text-muted">
                    {{ trans('entities.convert_book_confirm') }}
                    <br>
                    {{ trans('entities.convert_undo_warning') }}
                </li>
                <li>
                    <form action="{{ $book->getUrl('/convert-to-shelf') }}" method="POST">
                        {!! csrf_field() !!}
                        <button type="submit" class="text-link text-item">{{ trans('common.confirm') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
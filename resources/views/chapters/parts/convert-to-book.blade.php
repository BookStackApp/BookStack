<div class="content-wrap card auto-height">
    <h2 class="list-heading">Convert to Book</h2>
    <div class="grid half left-focus no-row-gap">
        <p>
            You can convert this chapter to a new book with the same contents.
            Any permissions set on this chapter will be copied to the new book but any inherited permissions,
            from the parent book, will not be copied which could lead to a change of access control.
        </p>
        <div class="text-m-right">
            <div component="dropdown" class="dropdown-container">
                <button refs="dropdown@toggle" class="button outline" aria-haspopup="true" aria-expanded="false">Convert Chapter</button>
                <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                    <li class="px-m py-s text-small text-muted">
                        Are you sure you want to convert this chapter?
                        <br>
                        This cannot be as easily undone.
                    </li>
                    <li>
                        <form action="{{ $chapter->getUrl('/convert-to-book') }}" method="POST">
                            {!! csrf_field() !!}
                            <button type="submit" class="text-primary text-item">{{ trans('common.confirm') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
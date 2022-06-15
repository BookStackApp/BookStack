<div class="content-wrap card auto-height">
    <h2 class="list-heading">Convert to Shelf</h2>
    <p>
        You can convert this book to a new shelf with the same contents.
        Chapters contained within this book will be converted to new books.

        If this book contains any pages, that are not in a chapter, this book will be renamed
        and contain such pages, and this book will become part of the new shelf.

        <br><br>

        Any permissions set on this book will be copied to the new shelf and to all new child books
        that don't have their own permissions enforced.

        Note that permissions on shelves do not auto-cascade to content within, as they do for books.
    </p>
    <div class="text-right">
        <div component="dropdown" class="dropdown-container">
            <button refs="dropdown@toggle" class="button outline" aria-haspopup="true" aria-expanded="false">Convert Book</button>
            <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                <li class="px-m py-s text-small text-muted">
                    Are you sure you want to convert this book?
                    <br>
                    This cannot be as easily undone.
                </li>
                <li>
                    <form action="{{ $book->getUrl('/convert-to-shelf') }}" method="POST">
                        {!! csrf_field() !!}
                        <button type="submit" class="text-primary text-item">{{ trans('common.confirm') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
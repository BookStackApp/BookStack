
{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form/text', ['name' => 'name'])
</div>

<div class="form-group description-input">
    <label for="description">{{ trans('common.description') }}</label>
    @include('form/textarea', ['name' => 'description'])
</div>

<div shelf-sort class="row">
    <div class="col-md-6">
        <div  class="form-group">
            <label for="books">{{ trans('entities.shelves_books') }}</label>
            <input type="hidden" id="books-input" name="books"
                   value="{{ isset($shelf) ? $shelf->books->implode('id', ',') : '' }}">
            <div class="scroll-box">
                <div class="scroll-box-item text-small text-muted instruction">
                    {{ trans('entities.shelves_drag_books') }}
                </div>
                <div class="scroll-box-item scroll-box-placeholder" style="display: none;">
                    <a href="#" class="text-muted">@icon('book') ...</a>
                </div>
                @if (isset($shelfBooks) && count($shelfBooks) > 0)
                    @foreach ($shelfBooks as $book)
                        <div data-id="{{ $book->id }}" class="scroll-box-item">
                            <a href="{{ $book->getUrl() }}" class="text-book">@icon('book'){{ $book->name }}</a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="books">{{ trans('entities.shelves_add_books') }}</label>
            <div class="scroll-box">
                @foreach ($books as $book)
                    <div data-id="{{ $book->id }}" class="scroll-box-item">
                        <a href="{{ $book->getUrl() }}" class="text-book">@icon('book'){{ $book->name }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>



<div class="form-group" collapsible id="logo-control">
    <div class="collapse-title text-primary" collapsible-trigger>
        <label for="user-avatar">{{ trans('common.cover_image') }}</label>
    </div>
    <div class="collapse-content" collapsible-content>
        <p class="small">{{ trans('common.cover_image_description') }}</p>

        @include('components.image-picker', [
            'resizeHeight' => '512',
            'resizeWidth' => '512',
            'showRemove' => false,
            'defaultImage' => baseUrl('/book_default_cover.png'),
            'currentImage' => isset($shelf) ? $shelf->getBookCover() : baseUrl('/book_default_cover.png') ,
            'currentId' => isset($shelf) && $shelf->image_id ? $shelf->image_id : 0,
            'name' => 'image_id',
            'imageClass' => 'cover'
        ])
    </div>
</div>

<div class="form-group" collapsible id="tags-control">
    <div class="collapse-title text-primary" collapsible-trigger>
        <label for="tag-manager">{{ trans('entities.shelf_tags') }}</label>
    </div>
    <div class="collapse-content" collapsible-content>
        @include('components.tag-manager', ['entity' => $shelf ?? null, 'entityType' => 'bookshelf'])
    </div>
</div>

<div class="form-group text-right">
    <a href="{{ isset($shelf) ? $shelf->getUrl() : baseUrl('/shelves') }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button pos">{{ trans('entities.shelves_save') }}</button>
</div>
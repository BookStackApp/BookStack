
{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form.text', ['name' => 'name'])
</div>

<div class="form-group description-input">
    <label for="description">{{ trans('common.description') }}</label>
    @include('form.textarea', ['name' => 'description'])
</div>

<div class="form-group" collapsible id="logo-control">
    <div class="collapse-title text-primary" collapsible-trigger>
        <label for="user-avatar">{{ trans('common.cover_image') }}</label>
    </div>
    <div class="collapse-content" collapsible-content>
        <p class="small">{{ trans('common.cover_image_description') }}</p>

        @include('components.image-picker', [
            'defaultImage' => url('/book_default_cover.png'),
            'currentImage' => (isset($model) && $model->cover) ? $model->getBookCover() : url('/book_default_cover.png') ,
            'name' => 'image',
            'imageClass' => 'cover'
        ])
    </div>
</div>

<div class="form-group" collapsible id="tags-control">
    <div class="collapse-title text-primary" collapsible-trigger>
        <label for="tag-manager">{{ trans('entities.book_tags') }}</label>
    </div>
    <div class="collapse-content" collapsible-content>
        @include('components.tag-manager', ['entity' => isset($book)?$book:null, 'entityType' => 'chapter'])
    </div>
</div>

<div class="form-group text-right">
    <a href="{{ isset($book) ? $book->getUrl() : url('/books') }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button primary">{{ trans('entities.books_save') }}</button>
</div>
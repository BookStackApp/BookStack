
{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form/text', ['name' => 'name'])
</div>

<div class="form-group description-input">
    <label for="description">{{ trans('common.description') }}</label>
    @include('form/textarea', ['name' => 'description'])
</div>
<div class="form-group" id="logo-control">
        <label for="user-avatar">{{ trans('common.cover_image') }}</label>
        <p class="small">{{ trans('common.cover_image_description') }}</p>

        @include('components.image-picker', [
            'resizeHeight' => '192',
            'resizeWidth' => '120',
            'showRemove' => true,
            'defaultImage' => baseUrl('/default.png'),
            'currentImage' => @isset($model) ? $model->getBookCover(80) : baseUrl('/default.png') ,
            'currentId' => @isset($model) ? $model->image : 0,
            'name' => 'image',
            'imageClass' => 'cover'
        ])
</div>
<div class="form-group">
    <a href="{{ isset($book) ? $book->getUrl() : baseUrl('/books') }}" class="button muted">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button pos">{{ trans('entities.books_save') }}</button>
</div>
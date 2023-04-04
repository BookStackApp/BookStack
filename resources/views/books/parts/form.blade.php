
{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form.text', ['name' => 'name', 'autofocus' => true])
</div>

<div class="form-group description-input">
    <label for="description">{{ trans('common.description') }}</label>
    @include('form.textarea', ['name' => 'description'])
</div>

<div class="form-group collapsible" component="collapsible" id="logo-control">
    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
        <label>{{ trans('common.cover_image') }}</label>
    </button>
    <div refs="collapsible@content" class="collapse-content">
        <p class="small">{{ trans('common.cover_image_description') }}</p>

        @include('form.image-picker', [
            'defaultImage' => url('/book_default_cover.png'),
            'currentImage' => (isset($model) && $model->cover) ? $model->getBookCover() : url('/book_default_cover.png') ,
            'name' => 'image',
            'imageClass' => 'cover'
        ])
    </div>
</div>

<div class="form-group collapsible" component="collapsible" id="docs-control">
    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
        <label>{{ trans('common.document_file') }}</label>
    </button>
    <div refs="collapsible@content" class="collapse-content">
        <p class="small">{{ trans('common.select_document') }}</p>
        <input type="file" id="fileInput" name="document_file" accept=".docx">
        <input type="hidden" id="html_input" name="html_input" value="">
        @if($errors->has('document_file'))
         <div class="text-neg text-small">{{ $errors->first('document_file') }}</div>
        @endif
        @if(isset($model))
        <div class="my-s" id="docs-option-control" @if(!$errors->has('document_option')) style="display:none" @endif>
            <p>{{ trans('common.select_document_option') }}</p>
            <label for="append-to-existing">
              <input type="radio" id="append-to-existing" name="document_option" value="append"> {{ trans('common.append_to') }}
            </label>
            <label for="create-new">
                <input type="radio" id="create-new" name="document_option" value="new"> {{ trans('common.create_new_pages') }}
            </label>
            @if($errors->has('document_option'))
             <div class="text-neg text-small">{{ $errors->first('document_option') }}</div>
           @endif
        </div>
        @endif
    </div>
</div>

<div class="form-group collapsible" component="collapsible" id="tags-control">
    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
        <label for="tag-manager">{{ trans('entities.book_tags') }}</label>
    </button>
    <div refs="collapsible@content" class="collapse-content">
        @include('entities.tag-manager', ['entity' => $book ?? null])
    </div>
</div>

<div class="form-group text-right">
    <a href="{{ $returnLocation }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button">{{ trans('entities.books_save') }}</button>
</div>
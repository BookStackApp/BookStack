
{!! csrf_field() !!}

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
        <label for="user-avatar">{{ trans('entities.chapter_tags') }}</label>
    </div>
    <div class="collapse-content" collapsible-content>
        @include('components.tag-manager', ['entity' => isset($chapter)?$chapter:null, 'entityType' => 'chapter'])
    </div>
</div>

<div class="form-group text-right">
    <a href="{{ isset($chapter) ? $chapter->getUrl() : $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button">{{ trans('entities.chapters_save') }}</button>
</div>

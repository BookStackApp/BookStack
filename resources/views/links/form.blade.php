
{!! csrf_field() !!}

<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form/text', ['name' => 'name'])
</div>

<div class="form-group title-input">
    <label for="link_to">URL</label>
    @include('form/text', ['name' => 'link_to'])
</div>

<div class="form-group text-right">
    <a href="{{ isset($chapter) ? $chapter->getUrl() : $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button pos">{{ trans('entities.link_save') }}</button>
</div>

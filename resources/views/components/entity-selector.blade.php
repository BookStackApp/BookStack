<div class="form-group">
    <div entity-selector class="entity-selector {{$selectorSize or ''}}" entity-types="{{ $entityTypes or 'book,chapter,page' }}" entity-permission="{{ $entityPermission or 'view' }}">
        <input type="hidden" entity-selector-input name="{{$name}}" value="">
        <input type="text" placeholder="{{ trans('common.search') }}" entity-selector-search>
        <div class="text-center loading" entity-selector-loading>@include('partials.loading-icon')</div>
        <div entity-selector-results></div>
    </div>
</div>
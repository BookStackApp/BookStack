<div class="form-group">
    <div entity-selector class="entity-selector {{$selectorSize ?? ''}}" entity-types="{{ $entityTypes ?? 'book,chapter,page' }}" entity-permission="{{ $entityPermission ?? 'view' }}">
        <input type="hidden" entity-selector-input name="{{$name}}" value="">
        <input type="text" placeholder="{{ trans('common.search') }}" entity-selector-search>
        <div class="text-center loading" entity-selector-loading>@include('partials.loading-icon')</div>
        <div entity-selector-results></div>
        @if($showAdd ?? false)
            <div class="entity-selector-add">
                <button entity-selector-add-button type="button"
                        class="button outline">@icon('add'){{ trans('common.add') }}</button>
            </div>
        @endif
    </div>
</div>
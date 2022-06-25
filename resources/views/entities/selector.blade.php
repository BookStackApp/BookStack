<div class="form-group entity-selector-container">
    <div component="entity-selector"
         refs="entity-selector-popup@selector"
         class="entity-selector {{$selectorSize ?? ''}}"
         option:entity-selector:entity-types="{{ $entityTypes ?? 'book,chapter,page' }}"
         option:entity-selector:entity-permission="{{ $entityPermission ?? 'view' }}">
        <input refs="entity-selector@input" type="hidden" name="{{$name}}" value="">
        <input refs="entity-selector@search" type="text" placeholder="{{ trans('common.search') }}" @if($autofocus ?? false) autofocus @endif>
        <div class="text-center loading" refs="entity-selector@loading">@include('common.loading-icon')</div>
        <div refs="entity-selector@results"></div>
        @if($showAdd ?? false)
            <div class="entity-selector-add">
                <button refs="entity-selector@add" type="button"
                        class="button outline">@icon('add'){{ trans('common.add') }}</button>
            </div>
        @endif
    </div>
</div>
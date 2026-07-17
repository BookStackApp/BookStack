{{--
$name - string
$autofocus - boolean, optional
$entityTypes - string, optional
$entityPermission - string, optional
$selectorEndpoint - string, optional
$selectorSize - string, optional (compact)
--}}
<div class="form-group entity-selector-container">
    <div component="entity-selector"
         refs="entity-selector-popup@selector"
         class="entity-selector {{$selectorSize ?? ''}}"
         option:entity-selector:entity-types="{{ $entityTypes ?? 'book,chapter,page' }}"
         option:entity-selector:entity-permission="{{ $entityPermission ?? 'view' }}"
         option:entity-selector:search-endpoint="{{ $selectorEndpoint ?? '/search/entity-selector' }}">
        <input refs="entity-selector@input" type="hidden" name="{{$name}}" value="">
        <input refs="entity-selector@search" type="text" placeholder="{{ trans('common.search') }}" @if($autofocus ?? false) autofocus @endif>
        <div class="text-center loading" refs="entity-selector@loading">@include('common.loading-icon')</div>
        <div refs="entity-selector@results"></div>
    </div>
</div>
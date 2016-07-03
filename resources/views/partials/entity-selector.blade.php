<div class="form-group">
    <div entity-selector class="entity-selector {{$selectorSize or ''}}" entity-types="{{ $entityTypes or 'book,chapter,page' }}">
        <input type="hidden" entity-selector-input name="{{$name}}" value="">
        <input type="text" placeholder="Search" ng-model="search" ng-model-options="{debounce: 200}" ng-change="searchEntities()">
        <div class="text-center loading" ng-show="loading">@include('partials/loading-icon')</div>
        <div ng-show="!loading" ng-bind-html="entityResults"></div>
    </div>
</div>
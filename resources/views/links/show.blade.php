@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-8 col-xs-5 faded">
        <div class="breadcrumbs">
            <a href="{{ $book->getUrl() }}" class="text-book text-button">@icon('book'){{ $book->name }}</a>
            <span class="sep">&raquo;</span>
            <span class="text-button">{{ $link->name }}</span>
        </div>
    </div>
    <div class="col-sm-4 col-xs-7 faded">
        <div class="action-buttons">
            @if(userCan('link-update', $link))
                <a href="{{ $link->getUrl('/edit') }}" class="text-primary text-button" >@icon('edit'){{ trans('common.edit') }}</a>
                <a href="{{ $link->getUrl('/delete') }}" class="text-primary text-button">@icon('delete'){{ trans('common.delete') }}</a>
            @endif
        </div>
    </div>
@stop

@section('sidebar')
    @include('partials/book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop

@section('body')
    <div class="link-content" ng-non-bindable>

        <div class="pointer-container" id="pointer">
            <div class="pointer anim" >
                <span class="icon text-primary">@icon('link') @icon('include', ['style' => 'display:none;'])</span>
                <input readonly="readonly" type="text" id="pointer-url" placeholder="url">
                <button class="button icon" data-clipboard-target="#pointer-url" type="button" title="{{ trans('entities.links_copy_link') }}">@icon('copy')</button>
            </div>
        </div>

        <div ng-non-bindable>
            <h1 class="break-text" v-pre id="bkmrk-page-title">{{$link->name}}</h1>
            <div style="clear:left;"></div>

            <a href="{{$link->link_to}}" target="_blank">{{$link->link_to}}</a>
        </div>

    </div>
@stop
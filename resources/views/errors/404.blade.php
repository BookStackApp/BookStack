@extends('simple-layout')

@section('content')
<div class="container">

    <p>&nbsp;</p>

    <div class="card">
        <h3>@icon('danger') {{ $message or trans('errors.404_page_not_found') }}</h3>
        <div class="body">
            <h5>{{ trans('errors.sorry_page_not_found') }}</h5>
            <p><a href="{{ baseUrl('/') }}" class="button outline">{{ trans('errors.return_home') }}</a></p>
        </div>
    </div>

    @if (setting('app-public') || !user()->isDefault())
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="text-muted">@icon('page') {{ trans('entities.pages_popular') }}</h3>
                    @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, [\BookStack\Page::class]), 'style' => 'compact'])
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h3 class="text-muted">@icon('book') {{ trans('entities.books_popular') }}</h3>
                    @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, [\BookStack\Book::class]), 'style' => 'compact'])
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <h3 class="text-muted">@icon('chapter') {{ trans('entities.chapters_popular') }}</h3>
                    @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, [\BookStack\Chapter::class]), 'style' => 'compact'])
                </div>
            </div>
        </div>
    @endif
</div>

@stop
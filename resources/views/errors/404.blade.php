@extends('simple-layout')

@section('content')
<div class="container mt-l">

    <div class="card mb-xl px-l pb-xl pt-l">
        <div class="grid half v-center">
            <div>
                <h1 class="list-heading">{{ $message ?? trans('errors.404_page_not_found') }}</h1>
                <h5>{{ trans('errors.sorry_page_not_found') }}</h5>
            </div>
            <div class="text-right">
                <a href="{{ baseUrl('/') }}" class="button outline">{{ trans('errors.return_home') }}</a>
            </div>
        </div>

    </div>

    @if (setting('app-public') || !user()->isDefault())
        <div class="grid third gap-xxl">
            <div>
                <div class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.pages_popular') }}</h3>
                    <div class="px-m">
                        @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, 'page'), 'style' => 'compact'])
                    </div>
                </div>
            </div>
            <div>
                <div class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.books_popular') }}</h3>
                    <div class="px-m">
                        @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, 'book'), 'style' => 'compact'])
                    </div>
                </div>
            </div>
            <div>
                <div class="card mb-xl">
                    <h3 class="card-title">{{ trans('entities.chapters_popular') }}</h3>
                    <div class="px-m">
                        @include('partials.entity-list', ['entities' => Views::getPopular(10, 0, 'chapter'), 'style' => 'compact'])
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@stop
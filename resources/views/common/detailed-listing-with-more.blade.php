@extends('layouts.simple')

@section('body')
    <div class="container small pt-xl">
        <main class="card content-wrap">
            <h1 class="list-heading">{{ $title }}</h1>

            <div class="book-contents">
                @include('entities.list', ['entities' => $entities, 'style' => 'detailed'])
            </div>

            <div class="text-right">
                @if($hasMoreLink)
                    <a href="{{ $hasMoreLink }}" class="button outline">{{ trans('common.more') }}</a>
                @endif
            </div>
        </main>
    </div>
@stop
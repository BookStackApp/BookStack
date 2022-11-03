@extends('layouts.simple')

@section('body')
    <div class="container small">

        <main class="card content-wrap mt-xxl">

            <h1 class="list-heading">{{ trans('entities.tags') }}</h1>

            <p class="text-muted">{{ trans('entities.tags_index_desc') }}</p>

            <div class="flex-container-row wrap justify-space-between items-center mb-s gap-m">
                <div class="block inline mr-xs">
                    <form method="get" action="{{ url("/tags") }}">
                        @include('form.request-query-inputs', ['params' => ['name']])
                        <input type="text"
                               name="search"
                               placeholder="{{ trans('common.search') }}"
                               value="{{ $listOptions->getSearch() }}">
                    </form>
                </div>
                <div class="block inline">
                    @include('common.sort', $listOptions->getSortControlData())
                </div>
            </div>

            @if($nameFilter)
                <div class="my-m">
                    <strong class="mr-xs">{{ trans('common.filter_active') }}</strong>
                    @include('entities.tag', ['tag' => new \BookStack\Actions\Tag(['name' => $nameFilter])])
                    <form method="get" action="{{ url("/tags") }}" class="inline block">
                        @include('form.request-query-inputs', ['params' => ['search']])
                        <button class="text-button text-warn">@icon('close'){{ trans('common.filter_clear') }}</button>
                    </form>
                </div>
            @endif

            @if(count($tags) > 0)
                <div class="item-list mt-m">
                    @foreach($tags as $tag)
                        @include('tags.parts.tags-list-item', ['tag' => $tag, 'nameFilter' => $nameFilter])
                    @endforeach
                </div>

                <div class="my-m">
                    {{ $tags->links() }}
                </div>
            @else
                <p class="text-muted italic my-xl">
                    {{ trans('common.no_items') }}.
                    <br>
                    {{ trans('entities.tags_list_empty_hint') }}
                </p>
            @endif
        </main>

    </div>

@stop

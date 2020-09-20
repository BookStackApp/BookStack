@extends('simple-layout')

@section('body')
    <div class="container mt-xl" id="search-system">

        <div class="grid right-focus reverse-collapse gap-xl">
            <div>
                <div>
                    <h5>{{ trans('entities.search_advanced') }}</h5>

                    <form method="get" action="{{ url('/search') }}">
                        <h6>{{ trans('entities.search_terms') }}</h6>
                        <input type="text" name="search" value="{{ implode(' ', $options->searches) }}">

                        <h6>{{ trans('entities.search_content_type') }}</h6>
                        <div class="form-group">

                            <?php
                            $types = explode('|', $options->filters['type'] ?? '');
                            $hasTypes = $types[0] !== '';
                            ?>
                            @include('search.form.type-filter', ['checked' => !$hasTypes || in_array('page', $types), 'entity' => 'page', 'transKey' => 'page'])
                            @include('search.form.type-filter', ['checked' => !$hasTypes || in_array('chapter', $types), 'entity' => 'chapter', 'transKey' => 'chapter'])
                            <br>
                                @include('search.form.type-filter', ['checked' => !$hasTypes || in_array('book', $types), 'entity' => 'book', 'transKey' => 'book'])
                                @include('search.form.type-filter', ['checked' => !$hasTypes || in_array('bookshelf', $types), 'entity' => 'bookshelf', 'transKey' => 'shelf'])
                        </div>

                        <h6>{{ trans('entities.search_exact_matches') }}</h6>
                        @include('search.form.term-list', ['type' => 'exact', 'currentList' => $options->exacts])

                        <h6>{{ trans('entities.search_tags') }}</h6>
                        @include('search.form.term-list', ['type' => 'tags', 'currentList' => $options->tags])

                        @if(signedInUser())
                            <h6>{{ trans('entities.search_options') }}</h6>

                            @component('search.form.boolean-filter', ['filters' => $options->filters, 'name' => 'viewed_by_me', 'value' => null])
                                {{ trans('entities.search_viewed_by_me') }}
                            @endcomponent
                            @component('search.form.boolean-filter', ['filters' => $options->filters, 'name' => 'not_viewed_by_me', 'value' => null])
                                {{ trans('entities.search_not_viewed_by_me') }}
                            @endcomponent
                            @component('search.form.boolean-filter', ['filters' => $options->filters, 'name' => 'is_restricted', 'value' => null])
                                {{ trans('entities.search_permissions_set') }}
                            @endcomponent
                            @component('search.form.boolean-filter', ['filters' => $options->filters, 'name' => 'created_by', 'value' => 'me'])
                                {{ trans('entities.search_created_by_me') }}
                            @endcomponent
                            @component('search.form.boolean-filter', ['filters' => $options->filters, 'name' => 'updated_by', 'value' => 'me'])
                                {{ trans('entities.search_updated_by_me') }}
                            @endcomponent
                        @endif

                        <h6>{{ trans('entities.search_date_options') }}</h6>
                        @include('search.form.date-filter', ['name' => 'updated_after', 'filters' => $options->filters])
                        @include('search.form.date-filter', ['name' => 'updated_before', 'filters' => $options->filters])
                        @include('search.form.date-filter', ['name' => 'created_after', 'filters' => $options->filters])
                        @include('search.form.date-filter', ['name' => 'created_before', 'filters' => $options->filters])

                        <button type="submit" class="button">{{ trans('entities.search_update') }}</button>
                    </form>

                </div>
            </div>
            <div>
                <div class="card content-wrap">
                    <h1 class="list-heading">{{ trans('entities.search_results') }}</h1>

                    <form action="{{ url('/search') }}" method="GET"  class="search-box flexible hide-over-l">
                        <input value="{{$searchTerm}}" type="text" name="term" placeholder="{{ trans('common.search') }}">
                        <button type="submit">@icon('search')</button>
                    </form>

                    <h6 class="text-muted">{{ trans_choice('entities.search_total_results_found', $totalResults, ['count' => $totalResults]) }}</h6>
                    <div class="book-contents">
                        @include('partials.entity-list', ['entities' => $entities, 'showPath' => true])
                    </div>

                    @if($hasNextPage)
                        <div class="text-right mt-m">
                            <a href="{{ $nextPageLink }}" class="button outline">{{ trans('entities.search_more') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@stop

{{--
$favourites - array
--}}
@if(count($favourites) > 0)
    <div id="top-favourites" class="mb-xl">
        <h5>{{ trans('entities.my_most_viewed_favourites') }}</h5>
        @include('entities.list', [
            'entities' => $favourites,
            'style' => 'compact',
        ])
        <a href="{{ url('/favourites')  }}" class="text-muted block py-xs">{{ trans('common.view_all') }}</a>
    </div>
@endif
{{--
$favourites - array
--}}
@if(count($favourites) > 0)
    <div id="top-favourites" class="card mb-xl">
        <h3 class="card-title">{{ trans('entities.my_most_viewed_favourites') }}</h3>
        <div class="px-m">
            @include('entities.list', [
            'entities' => $favourites,
            'style' => 'compact',
            ])
        </div>
        <a href="{{ url('/favourites')  }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
    </div>
@endif
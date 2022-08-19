<main class="card content-wrap">
    <h1 class="list-heading">{{ trans('entities.references') }}</h1>
    <p>{{ trans('entities.references_to_desc') }}</p>

    @if(count($references) > 0)
        <div class="book-contents">
            @include('entities.list', ['entities' => $references->pluck('from'), 'showPath' => true])
        </div>
    @else
        <p class="text-muted italic">{{ trans('entities.references_none') }}</p>
    @endif

</main>
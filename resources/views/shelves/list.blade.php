
<div class="container{{ $view === 'list' ? ' small' : '' }}">
    {{--TODO - Align with books page, Have sorting operations--}}
    {{--TODO - Create unique list item--}}
    <h1>{{ trans('entities.shelves') }}</h1>
    @if(count($shelves) > 0)
        @if($view === 'grid')
            <div class="grid third">
                @foreach($shelves as $key => $shelf)
                    @include('shelves/grid-item', ['bookshelf' => $shelf])
                @endforeach
            </div>
        @else
            @foreach($shelves as $shelf)
                @include('shelves/list-item', ['bookshelf' => $shelf])
                <hr>
            @endforeach
        @endif
        <div>
            {!! $shelves->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.shelves_empty') }}</p>
        @if(userCan('bookshelf-create-all'))
            <a href="{{ baseUrl("/create-shelf") }}" class="button outline">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif
</div>
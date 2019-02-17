<div class="breadcrumbs text-center">
    <?php $breadcrumbCount = 0; ?>
    @foreach($crumbs as $key => $crumb)
        @if (is_null($crumb))
            <?php continue; ?>
        @endif
        @if ($breadcrumbCount !== 0)
            <div class="separator">@icon('chevron-right')</div>
        @endif

        @if (is_string($crumb))
            <a href="{{  baseUrl($key)  }}">
                {{ $crumb }}
            </a>
        @elseif (is_array($crumb))
            <a href="{{  baseUrl($key)  }}">
                @icon($crumb['icon']) {{ $crumb['text'] }}
            </a>
        @elseif($crumb instanceof \BookStack\Entities\Entity)
            <a href="{{ $crumb->getUrl() }}" class="text-{{$crumb->getType()}}">
                @icon($crumb->getType()){{ $crumb->getShortName() }}
            </a>
        @endif
        <?php $breadcrumbCount++; ?>
    @endforeach

    {{--@if (isset($book) && userCan('view', $book))--}}
        {{--<a href="{{ $book->getUrl() }}" class="text-book">--}}
            {{--@icon('book'){{ $book->getShortName() }}--}}
        {{--</a>--}}
        {{--<div class="separator">@icon('chevron-right')</div>--}}
    {{--@endif--}}
    {{--@if(isset($chapter) && userCan('view', $chapter))--}}
        {{--<a href="{{ $chapter->getUrl() }}" class="text-chapter">--}}
            {{--@icon('chapter'){{ $chapter->getShortName() }}--}}
        {{--</a>--}}
        {{--@if (isset($page))--}}
            {{--<div class="separator">@icon('chevron-right')</div>--}}
        {{--@endif--}}
    {{--@endif--}}
    {{--@if(isset($page) && userCan('view', $page))--}}
        {{--<a href="{{ $page->getUrl() }}" class="text-page">--}}
            {{--@icon('page'){{ $page->getShortName() }}--}}
        {{--</a>--}}
    {{--@endif--}}
</div>
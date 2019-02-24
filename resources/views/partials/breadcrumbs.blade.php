<div class="breadcrumbs text-center">
    <?php $breadcrumbCount = 0; ?>

    {{--Show top level item--}}
    @if (count($crumbs) > 0 && $crumbs[0] instanceof  \BookStack\Entities\Book)
        <a href="{{  baseUrl('/books')  }}" class="icon-list-item">
            <span>@icon('books')</span>
            <span>{{ trans('entities.books') }}</span>
        </a>
        <?php $breadcrumbCount++; ?>
    @endif

    @foreach($crumbs as $key => $crumb)
        <?php $isEntity = ($crumb instanceof \BookStack\Entities\Entity); ?>

        @if (is_null($crumb))
            <?php continue; ?>
        @endif
        @if ($breadcrumbCount !== 0 && !$isEntity)
            <div class="separator">@icon('chevron-right')</div>
        @endif

        @if (is_string($crumb))
            <a href="{{  baseUrl($key)  }}">
                {{ $crumb }}
            </a>
        @elseif (is_array($crumb))
            <a href="{{  baseUrl($key)  }}" class="icon-list-item">
                <span>@icon($crumb['icon'])</span>
                <span>{{ $crumb['text'] }}</span>
            </a>
        @elseif($isEntity && userCan('view', $crumb))
            @if($breadcrumbCount > 0)
                @include('partials.breadcrumb-listing', ['entity' => $crumb])
            @endif
            <a href="{{ $crumb->getUrl() }}" class="text-{{$crumb->getType()}} icon-list-item">
                <span>@icon($crumb->getType())</span>
                <span>
                    {{ $crumb->getShortName() }}
                </span>
            </a>
        @endif
        <?php $breadcrumbCount++; ?>
    @endforeach
</div>
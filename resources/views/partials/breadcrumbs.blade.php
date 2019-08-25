<nav class="breadcrumbs text-center" aria-label="{{ trans('common.breadcrumb') }}">
    <?php $breadcrumbCount = 0; ?>

    {{-- Show top level books item --}}
    @if (count($crumbs) > 0 && array_first($crumbs) instanceof  \BookStack\Entities\Book)
        <a href="{{  url('/books')  }}" class="text-book icon-list-item outline-hover">
            <span>@icon('books')</span>
            <span>{{ trans('entities.books') }}</span>
        </a>
        <?php $breadcrumbCount++; ?>
    @endif

    {{-- Show top level shelves item --}}
    @if (count($crumbs) > 0 && array_first($crumbs) instanceof  \BookStack\Entities\Bookshelf)
        <a href="{{  url('/shelves')  }}" class="text-bookshelf icon-list-item outline-hover">
            <span>@icon('bookshelf')</span>
            <span>{{ trans('entities.shelves') }}</span>
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
            <a href="{{  url($key)  }}">
                {{ $crumb }}
            </a>
        @elseif (is_array($crumb))
            <a href="{{  url($key)  }}" class="icon-list-item outline-hover">
                <span>@icon($crumb['icon'])</span>
                <span>{{ $crumb['text'] }}</span>
            </a>
        @elseif($isEntity && userCan('view', $crumb))
            @if($breadcrumbCount > 0)
                @include('partials.breadcrumb-listing', ['entity' => $crumb])
            @endif
            <a href="{{ $crumb->getUrl() }}" class="text-{{$crumb->getType()}} icon-list-item outline-hover">
                <span>@icon($crumb->getType())</span>
                <span>
                    {{ $crumb->getShortName() }}
                </span>
            </a>
        @endif
        <?php $breadcrumbCount++; ?>
    @endforeach
</nav>
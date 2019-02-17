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
            <a href="{{  baseUrl($key)  }}" class="icon-list-item">
                <span>@icon($crumb['icon'])</span>
                <span>{{ $crumb['text'] }}</span>
            </a>
        @elseif($crumb instanceof \BookStack\Entities\Entity)
            <a href="{{ $crumb->getUrl() }}" class="text-{{$crumb->getType()}} icon-list-item">
                <span>@icon($crumb->getType())</span>
                <span>{{ $crumb->getShortName() }}</span>
            </a>
        @endif
        <?php $breadcrumbCount++; ?>
    @endforeach
</div>
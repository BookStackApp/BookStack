<div ng-non-bindable>

    <h1 id="bkmrk-page-title">{{$page->name}}</h1>

    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! isset($pageContent) ? $pageContent : $page->html !!}
    @endif
</div>
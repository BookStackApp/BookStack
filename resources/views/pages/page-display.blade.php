<div ng-non-bindable>

    <h1 id="bkmrk-page-title" class="float left">{{$page->name}}</h1>

    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! $page->html !!}
    @endif
</div>
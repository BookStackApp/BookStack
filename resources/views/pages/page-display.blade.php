<div ng-non-bindable>

    <h1 id="bkmrk-page-title" class="float left">{{$page->name}}</h1>

    @if(count($page->tags) > 0)
        <div class="tag-display float right">
            <table>
                <thead>
                    <tr class="text-left heading primary-background-light">
                        <th colspan="2">Page Tags</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page->tags as $tag)
                        <tr class="tag">
                            <td @if(!$tag->value) colspan="2" @endif><a href="{{ baseUrl('/search/all?term=%5B' . urlencode($tag->name) .'%5D') }}">{{ $tag->name }}</a></td>
                            @if($tag->value) <td class="tag-value"><a href="{{ baseUrl('/search/all?term=%5B' . urlencode($tag->name) .'%3D' . urlencode($tag->value) . '%5D') }}">{{$tag->value}}</a></td> @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! $page->html !!}
    @endif
</div>

<li data-id="{{$page['id']}}">{{ $page['name'] }}
    <ul>
        @if($page['hasChildren'])
            @foreach($page['pages'] as $childPage)
                @include('pages/page-tree-sort', ['page'=>$childPage])
            @endforeach
        @endif
    </ul>
</li>

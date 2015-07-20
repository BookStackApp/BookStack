

<ul class="nested-page-list">
    @foreach($pageTree as $subPage)
        <li @if($subPage['hasChildren'])class="has-children"@endif>
            @if($subPage['hasChildren'])
                <i class="fa fa-chevron-right arrow"></i>
            @endif
            <a href="{{$subPage['url']}}">{{$subPage['name']}}</a>
            @if($subPage['hasChildren'])
                @include('pages/page-tree-list', ['pageTree' => $subPage['pages']])
            @endif
        </li>
    @endforeach
</ul>
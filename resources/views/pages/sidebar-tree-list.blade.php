{{--Requires an array of pages to be passed as $pageTree--}}

<ul class="sidebar-page-list">
    @foreach($pageTree as $subPage)
        <li @if($subPage['hasChildren'])class="has-children"@endif>
            <a href="{{$subPage['url']}}" @if($subPage['current'])class="bold"@endif>{{$subPage['name']}}</a>
            @if($subPage['hasChildren'])
                @include('pages/sidebar-tree-list', ['pageTree' => $subPage['pages']])
            @endif
        </li>
    @endforeach
</ul>
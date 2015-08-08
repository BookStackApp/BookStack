@extends('base')

@section('content')

    <div class="page-content">
        <h1>{{ $book->name }} <span class="subheader">Sort Pages</span></h1>

        <ul class="sortable-page-list" id="sort-list">
            @foreach($book->children() as $bookChild)
                <li data-id="{{$bookChild->id}}" data-type="{{ is_a($bookChild, 'Oxbow\Chapter') ? 'chapter' : 'page' }}">
                    {{ $bookChild->name }}
                    @if(is_a($bookChild, 'Oxbow\Chapter'))
                        <ul>
                            @foreach($bookChild->pages as $page)
                                <li data-id="{{$page->id}}" data-type="page">
                                    {{ $page->name }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>

        <form action="{{$book->getUrl()}}/sort" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" id="sort-tree-input" name="sort-tree">
            <div class="list">
                <a href="{{$book->getUrl()}}" class="button muted">Cancel</a>
                <button class="button pos" type="submit">Save Ordering</button>
            </div>
        </form>

    </div>

    <script>
        $(document).ready(function() {

            var group = $('#sort-list').sortable({
                group: 'serialization',
                onDrop: function($item, container, _super) {
                    var data = group.sortable('serialize').get();
                    var pageMap = buildPageMap(data[0]);
                    $('#sort-tree-input').val(JSON.stringify(pageMap));
                    _super($item, container);
                }
            });

            function buildPageMap(data) {
                var pageMap = [];
                for(var i = 0; i < data.length; i++) {
                    var bookChild = data[i];
                    pageMap.push({
                        id: bookChild.id,
                        parentChapter: false,
                        type: bookChild.type
                    });
                    if(bookChild.type == 'chapter' && bookChild.children) {
                        var chapterId = bookChild.id;
                        var chapterChildren = bookChild.children[0];
                        for(var j = 0; j < chapterChildren.length; j++) {
                            var page = chapterChildren[j];
                            pageMap.push({
                                id: page.id,
                                parentChapter: chapterId,
                                type: 'page'
                            });
                        }
                    }
                }
                return pageMap;
            }

        });
    </script>
@stop

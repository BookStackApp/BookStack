@extends('base')

@section('content')

    <div class="row">
        <div class="page-menu col-md-3">
            <div class="page-actions">
                <form action="{{$book->getUrl()}}/sort" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="sort-tree-input" name="sort-tree">
                    <h4>Actions</h4>
                    <div class="list">
                        <button class="button pos" type="submit">Save Ordering</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="page-content right col-md-9">
            <h1>{{ $book->name }} <span class="subheader">Sort Pages</span></h1>

            <ul class="sortable-page-list" id="sort-list">
                @foreach($tree['pages'] as $treePage)
                    @include('pages/page-tree-sort', ['page' => $treePage])
                @endforeach
            </ul>

        </div>
    </div>

    <script>
        $(document).ready(function() {

            var group = $('#sort-list').sortable({
                group: 'serialization',
                onDrop: function($item, container, _super) {
                    var data = group.sortable('serialize').get();
                    console.log(data);
                    var pageMap = [];
                    var parent = 0;
                    buildPageMap(pageMap, parent, data[0]);
                    $('#sort-tree-input').val(JSON.stringify(pageMap));
                    _super($item, container);
                }
            });

            function buildPageMap(pageMap, parent, data) {
                for(var i = 0; i < data.length; i++) {
                    var page = data[i];
                    pageMap.push({
                        id: page.id,
                        parent: parent
                    });
                    buildPageMap(pageMap, page.id, page.children[0]);
                }
            }

        });
    </script>
@stop

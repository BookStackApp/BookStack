@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('books._breadcrumbs', ['book' => $book])
    </div>
@stop

@section('body')

    <div class="container" ng-non-bindable>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <h3>@icon('sort') {{ trans('entities.books_sort') }}</h3>
                    <div class="body">
                        <div id="sort-boxes">
                            @include('books/sort-box', ['book' => $book, 'bookChildren' => $bookChildren])
                        </div>

                        <form action="{{ $book->getUrl('/sort') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" id="sort-tree-input" name="sort-tree">
                            <div class="list">
                                <a href="{{ $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                                <button class="button pos" type="submit">{{ trans('entities.books_sort_save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if(count($books) > 1)
            <div class="col-md-4">
                <div class="card">
                    <h3>@icon('book') {{ trans('entities.books_sort_show_other') }}</h3>
                    <div class="body" id="additional-books">
                        @foreach($books as $otherBook)
                            @if($otherBook->id !== $book->id)
                                <div>
                                    <a href="{{ $otherBook->getUrl('/sort-item') }}" class="text-book">@icon('book'){{ $otherBook->name }}</a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ baseUrl("/libs/jquery-sortable/jquery-sortable.min.js") }}"></script>
    <script>
        $(document).ready(function() {

            var sortableOptions = {
                group: 'serialization',
                onDrop: function($item, container, _super) {
                    var pageMap = buildEntityMap();
                    $('#sort-tree-input').val(JSON.stringify(pageMap));
                    _super($item, container);
                },
                isValidTarget: function  ($item, container) {
                    // Prevent nested chapters
                    return !($item.is('[data-type="chapter"]') && container.target.closest('li').attr('data-type') == 'chapter');
                }
            };

            var group = $('.sort-list').sortable(sortableOptions);

            $('#additional-books').on('click', 'a', function(e) {
                e.preventDefault();
                var $link = $(this);
                var url = $link.attr('href');
                $.get(url, function(data) {
                    $('#sort-boxes').append(data);
                    group.sortable("destroy");
                    $('.sort-list').sortable(sortableOptions);
                });
                $link.remove();
            });

            /**
             * Build up a mapping of entities with their ordering and nesting.
             * @returns {Array}
             */
            function buildEntityMap() {
                var entityMap = [];
                var $lists = $('.sort-list');
                $lists.each(function(listIndex) {
                    var list = $(this);
                    var bookId = list.closest('[data-type="book"]').attr('data-id');
                    var $directChildren = list.find('> [data-type="page"], > [data-type="chapter"]');
                    $directChildren.each(function(directChildIndex) {
                        var $childElem = $(this);
                        var type = $childElem.attr('data-type');
                        var parentChapter = false;
                        var childId = $childElem.attr('data-id');
                        entityMap.push({
                            id: childId,
                            sort: directChildIndex,
                            parentChapter: parentChapter,
                            type: type,
                            book: bookId
                        });
                        $chapterChildren = $childElem.find('[data-type="page"]').each(function(pageIndex) {
                            var $chapterChild = $(this);
                            entityMap.push({
                                id: $chapterChild.attr('data-id'),
                                sort: pageIndex,
                                parentChapter: childId,
                                type: 'page',
                                book: bookId
                            });
                        });
                    });
                });
                return entityMap;
            }

        });
    </script>
@stop

@extends('simple-layout')

{{--TODO - Load books in via selector interface--}}

@section('body')

    <div class="container">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/sort') => [
                    'text' => trans('entities.books_sort'),
                    'icon' => 'sort',
                ]
            ]])
        </div>

        <div class="grid left-focus gap-xl">
            <div>
                <div class="card content-wrap">
                    <h1 class="list-heading">{{ trans('entities.books_sort') }}</h1>
                    <div id="sort-boxes">
                        @include('books.sort-box', ['book' => $book, 'bookChildren' => $bookChildren])
                    </div>

                    <form action="{{ $book->getUrl('/sort') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="sort-tree-input" name="sort-tree">
                        <div class="list text-right">
                            <a href="{{ $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button class="button primary" type="submit">{{ trans('entities.books_sort_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                @if(count($books) > 1)
                    <div class="card content-wrap">
                        <h2 class="list-heading">{{ trans('entities.books_sort_show_other') }}</h2>
                        <div id="additional-books">
                            @foreach($books as $otherBook)
                                @if($otherBook->id !== $book->id)
                                    <div>
                                        <a href="{{ $otherBook->getUrl('/sort-item') }}" class="text-book">@icon('book'){{ $otherBook->name }}</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

@stop

@section('scripts')
    <script src="{{ baseUrl("/libs/jquery-sortable/jquery-sortable.min.js") }}"></script>
    <script>
        $(document).ready(function() {

            const $container = $('#sort-boxes');

            // Sortable options
            const sortableOptions = {
                group: 'serialization',
                containerSelector: 'ul',
                itemPath: '',
                itemSelector: 'li',
                onDrop: function ($item, container, _super) {
                    updateMapInput();
                    _super($item, container);
                },
                isValidTarget: function ($item, container) {
                    // Prevent nested chapters
                    return !($item.is('[data-type="chapter"]') && container.target.closest('li').attr('data-type') === 'chapter');
                }
            };

            // Create our sortable group
            let group = $('.sort-list').sortable(sortableOptions);

            // Add additional books into the view on select.
            $('#additional-books').on('click', 'a', function(e) {
                e.preventDefault();

                const $link = $(this);
                const url = $link.attr('href');
                $.get(url, function(data) {
                    $container.append(data);
                    group.sortable("destroy");
                    group = $('.sort-list').sortable(sortableOptions);
                });
                $link.remove();
            });

            /**
             * Update the input with our sort data.
             */
            function updateMapInput() {
                const pageMap = buildEntityMap();
                $('#sort-tree-input').val(JSON.stringify(pageMap));
            }

            /**
             * Build up a mapping of entities with their ordering and nesting.
             * @returns {Array}
             */
            function buildEntityMap() {
                const entityMap = [];
                const $lists = $('.sort-list');
                $lists.each(function(listIndex) {
                    const $list = $(this);
                    const bookId = $list.closest('[data-type="book"]').attr('data-id');
                    const $directChildren = $list.find('> [data-type="page"], > [data-type="chapter"]');
                    $directChildren.each(function(directChildIndex) {
                        const $childElem = $(this);
                        const type = $childElem.attr('data-type');
                        const parentChapter = false;
                        const childId = $childElem.attr('data-id');

                        entityMap.push({
                            id: childId,
                            sort: directChildIndex,
                            parentChapter: parentChapter,
                            type: type,
                            book: bookId
                        });

                        $childElem.find('[data-type="page"]').each(function(pageIndex) {
                            const $chapterChild = $(this);
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


            // Auto sort control
            const sortOperations = {
                name: function(a, b) {
                    const aName = a.getAttribute('data-name').trim().toLowerCase();
                    const bName = b.getAttribute('data-name').trim().toLowerCase();
                    return aName.localeCompare(bName);
                },
                created: function(a, b) {
                    const aTime = Number(a.getAttribute('data-created'));
                    const bTime = Number(b.getAttribute('data-created'));
                    return bTime - aTime;
                },
                updated: function(a, b) {
                    const aTime = Number(a.getAttribute('data-update'));
                    const bTime = Number(b.getAttribute('data-update'));
                    return bTime - aTime;
                },
                chaptersFirst: function(a, b) {
                    const aType = a.getAttribute('data-type');
                    const bType = b.getAttribute('data-type');
                    if (aType === bType) {
                        return 0;
                    }
                    return (aType === 'chapter' ? -1 : 1);
                },
                chaptersLast: function(a, b) {
                    const aType = a.getAttribute('data-type');
                    const bType = b.getAttribute('data-type');
                    if (aType === bType) {
                        return 0;
                    }
                    return (aType === 'chapter' ? 1 : -1);
                },
            };

            let lastSort = '';
            let reverse = false;
            const reversableTypes = ['name', 'created', 'updated'];

            $container.on('click', '.sort-box-options [data-sort]', function(event) {
                event.preventDefault();
                const $sortLists = $(this).closest('.sort-box').find('ul');
                const sort = $(this).attr('data-sort');

                reverse = (lastSort === sort) ? !reverse : false;
                let sortFunction = sortOperations[sort];
                if (reverse && reversableTypes.includes(sort)) {
                   sortFunction = function(a, b) {
                       return 0 - sortOperations[sort](a, b)
                   };
                }

                $sortLists.each(function() {
                    const $list = $(this);
                    $list.children('li').sort(sortFunction).appendTo($list);
                });

                lastSort = sort;
                updateMapInput();
            });

        });
    </script>
@stop

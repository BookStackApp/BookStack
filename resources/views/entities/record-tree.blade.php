<nav id="record-tree"
     class="record-tree mb-xl"
     aria-label="{{ trans('entities.records_navigation') }}">

    <h5>{{ trans('entities.records_navigation') }}</h5>

    <ul class="sidebar-page-list mt-xs menu entity-list">
        @if (userCan('view', $record))
            <li class="list-item-record record">
                @include('entities.list-item-basic', ['entity' => $record, 'classes' => ($current->matches($record)? 'selected' : '')])
            </li>
        @endif

        @foreach($sidebarTree as $recordChild)
            <li class="list-item-{{ $recordChild->getType() }} {{ $recordChild->getType() }} {{ $recordChild->isA('page') && $recordChild->draft ? 'draft' : '' }}">
                @include('entities.list-item-basic', ['entity' => $recordChild, 'classes' => $current->matches($recordChild)? 'selected' : ''])

                @if($recordChild->isA('chapter') && count($recordChild->visible_pages) > 0)
                    <div class="entity-list-item no-hover">
                        <span role="presentation" class="icon text-chapter"></span>
                        <div class="content">
                            @include('chapters.parts.child-menu', [
                                'chapter' => $recordChild,
                                'current' => $current,
                                'isOpen'  => $recordChild->matchesOrContains($current)
                            ])
                        </div>
                    </div>

                @endif

            </li>
        @endforeach
    </ul>
</nav>

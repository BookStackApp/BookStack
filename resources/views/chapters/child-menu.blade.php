<div class="chapter-child-menu">
    <p chapter-toggle class="text-muted @if($bookChild->matchesOrContains($current)) open @endif">
        @icon('caret-right') @icon('page') <span>{{ trans_choice('entities.x_pages', $bookChild->pages->count()) }}</span>
    </p>
    <ul class="sub-menu inset-list @if($bookChild->matchesOrContains($current)) open @endif">
        @foreach($bookChild->pages as $childPage)
            <li class="list-item-page {{ $childPage->isA('page') && $childPage->draft ? 'draft' : '' }}">
                @include('partials.entity-list-item-basic', ['entity' => $childPage, 'classes' => $current->matches($childPage)? 'selected' : '' ])
            </li>
        @endforeach
    </ul>
</div>
<div component="chapter-contents" class="chapter-child-menu">
    <button type="button"
            refs="chapter-contents@toggle"
            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
            class="text-muted chapter-contents-toggle @if($isOpen) open @endif">
        @icon('caret-right') @icon('page') <span>{{ trans_choice('entities.x_pages', $bookChild->visible_pages->count()) }}</span>
    </button>
    <ul refs="chapter-contents@list"
        class="chapter-contents-list sub-menu inset-list @if($isOpen) open @endif" @if($isOpen)
        style="display: block;" @endif
        role="menu">
        @foreach($bookChild->visible_pages as $childPage)
            <li class="list-item-page {{ $childPage->isA('page') && $childPage->draft ? 'draft' : '' }}" role="presentation">
                @include('entities.list-item-basic', ['entity' => $childPage, 'classes' => $current->matches($childPage)? 'selected' : '' ])
            </li>
        @endforeach
    </ul>
</div>
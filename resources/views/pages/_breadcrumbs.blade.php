@include('partials.breadcrumbs', [
        'page' => $page,
        'chapter' => $page->hasChapter() ? $page->chapter : null,
        'book' => $page->book,
])
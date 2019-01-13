@include('partials.breadcrumbs', ['crumbs' => [
        $page->book,
        $page->hasChapter() ? $page->chapter : null,
        $page,
]])
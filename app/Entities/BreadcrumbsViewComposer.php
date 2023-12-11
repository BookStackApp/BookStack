<?php

namespace BookStack\Entities;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\ShelfContext;
use Illuminate\View\View;

class BreadcrumbsViewComposer
{
    public function __construct(
        protected ShelfContext $shelfContext
    ) {
    }

    /**
     * Modify data when the view is composed.
     */
    public function compose(View $view): void
    {
        $crumbs = $view->getData()['crumbs'];
        $firstCrumb = $crumbs[0] ?? null;

        if ($firstCrumb instanceof Book) {
            $shelf = $this->shelfContext->getContextualShelfForBook($firstCrumb);
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}

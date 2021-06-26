<?php

namespace BookStack\Entities;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\ShelfContext;
use Illuminate\View\View;

class BreadcrumbsViewComposer
{
    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     *
     * @param ShelfContext $entityContextManager
     */
    public function __construct(ShelfContext $entityContextManager)
    {
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Modify data when the view is composed.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $crumbs = $view->getData()['crumbs'];
        $firstCrumb = $crumbs[0] ?? null;
        if ($firstCrumb instanceof Book) {
            $shelf = $this->entityContextManager->getContextualShelfForBook($firstCrumb);
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}

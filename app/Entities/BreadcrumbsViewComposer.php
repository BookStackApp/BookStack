<?php namespace BookStack\Entities;

use Illuminate\View\View;

class BreadcrumbsViewComposer
{

    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     * @param EntityContextManager $entityContextManager
     */
    public function __construct(EntityContextManager $entityContextManager)
    {
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Modify data when the view is composed.
     * @param View $view
     */
    public function compose(View $view)
    {
        $crumbs = $view->getData()['crumbs'];
        if (array_first($crumbs) instanceof Book) {
            $shelf = $this->entityContextManager->getContextualShelfForBook(array_first($crumbs));
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}

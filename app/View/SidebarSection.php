<?php

namespace BookStack\View;

abstract class SidebarSection implements SidebarSectionInterface
{
    protected string $view;

    /**
     * @inheritDoc
     */
    public function getView(): string
    {
        return $this->view;
    }
}

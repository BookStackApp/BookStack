<?php

namespace BookStack\View;

abstract class ViewBlock implements ViewBlockInterface
{
    protected string $view;

    /**
     * @inheritDoc
     */
    public function getView(array $viewData): string
    {
        return $this->view;
    }
}

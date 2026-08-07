<?php

namespace BookStack\View;

abstract class ViewBlock implements ViewBlockInterface
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

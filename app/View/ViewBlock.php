<?php

namespace BookStack\View;

abstract class ViewBlock implements ViewBlockInterface
{
    protected string $id;
    protected string $view;
    protected string $labelTranslationKey;

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getView(array $viewData): string
    {
        return $this->view;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return trans($this->labelTranslationKey);
    }
}

<?php

namespace BookStack\View;

abstract class ViewBlock implements ViewBlockInterface
{
    protected static string $id;
    protected static string $view;
    protected static string $labelTranslationKey;

    /**
     * @inheritDoc
     */
    public static function getId(): string
    {
        return static::$id;
    }

    /**
     * @inheritDoc
     */
    public static function getLabel(): string
    {
        return trans(static::$labelTranslationKey);
    }

    /**
     * @inheritDoc
     */
    public function getView(array $viewData): string
    {
        return static::$view;
    }
}

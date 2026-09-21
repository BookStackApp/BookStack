<?php

namespace BookStack\View;

interface ViewBlockInterface
{
    /**
     * Get the unique ID for this block.
     */
    public static function getId(): string;

    /**
     * Get the label for this block.
     */
    public static function getLabel(): string;

    /**
     * Get the view to render for this block.
     * This is provided with the existing parent view data to allow the view used to be
     * dynamic based on the context of its environment.
     */
    public function getView(array $viewData): string;

    /**
     * Specify the data to pass to the view on render.
     * This is provided with the existing parent view data.
     * @return array<string, mixed>
     */
    public function getViewData(array $viewData): array;
}

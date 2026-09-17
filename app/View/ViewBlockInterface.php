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
     * Is provided with the existing parent view data.
     */
    public function getView(array $viewData): string;

    /**
     * Specify the data to pass to the view on render.
     * Is provided with the existing parent view data and the original request.
     * @return array<string, mixed>
     */
    public function withData(array $viewData): array;
}

<?php

namespace BookStack\View;

use Illuminate\Http\Request;

interface ViewBlockInterface
{
    /**
     * Get the view to render for this block.
     */
    public function getView(): string;

    /**
     * Specify the data to pass to the view on render.
     * Is provided with the existing parent view data and the original request.
     * @return array<string, mixed>
     */
    public function withData(array $viewData, Request $request): array;
}

<?php

namespace BookStack\View;

use Illuminate\Http\Request;

interface SidebarSectionInterface
{
    /**
     * Get the view to render for this section.
     */
    public function getView(): string;

    /**
     * Specify the data to pass to the view on render.
     * Is provided with the existing parent view data and the original request.
     * @return array<string, mixed>
     */
    public function withData(array $viewData, Request $request): array;
}

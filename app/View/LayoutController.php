<?php

namespace BookStack\View;

use Illuminate\Http\Request;

class LayoutController
{
    public function __construct(
        protected ViewBlockManager $viewBlocks
    ) {
    }

    /**
     * Start editing the layout for a specific location.
     */
    public function edit(string $location)
    {
        $namedLocations = $this->viewBlocks->getNamedLocations();
        $locationName = $namedLocations[$location] ?? $location;

        return view('settings.layouts.edit', [
            'location' => $location,
            'locationName' => $locationName,
            'namedLocations' => $namedLocations,
        ]);
    }

    /**
     * Update the layout for a specific location.
     */
    public function update(string $location, Request $request)
    {
        // TODO
    }
}

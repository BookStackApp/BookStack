<?php

namespace BookStack\View\SidebarSections;

use BookStack\Entities\Models\Bookshelf;
use BookStack\References\ReferenceFetcher;
use BookStack\View\SidebarSection;
use Illuminate\Http\Request;

class ShelvesShowDetails extends SidebarSection
{
    protected string $view = 'shelves.parts.show-sidebar-section-details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function withData(array $viewData, Request $request): array
    {
        /** @var Bookshelf $shelf */
        $shelf = $viewData['shelf'];
        $referenceCount = $this->referenceFetcher->getReferenceCountToEntity($shelf);

        return [
            'shelf' => $shelf,
            'referenceCount' => $referenceCount,
        ];
    }
}

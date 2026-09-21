<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Bookshelf;
use BookStack\References\ReferenceFetcher;
use BookStack\View\SimpleViewBlock;

class ShelvesShowDetails extends SimpleViewBlock
{
    protected static string $id = 'builtin_shelves-show-details';
    protected static string $view = 'shelves.parts.show-sidebar-section-details';
    protected static string $labelTranslationKey = 'common.details';

    public function __construct(
        protected ReferenceFetcher $referenceFetcher,
    ) {
    }

    public function getViewData(array $viewData): array
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

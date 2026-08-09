<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Chapter;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowTags extends ViewBlock
{
    protected string $view = 'chapters.parts.show-sidebar-section-tags';

    public function withData(array $viewData): array
    {
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        return [
            'chapter' => $chapter,
        ];
    }
}

<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Chapter;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowTags extends ViewBlock
{
    protected static string $id = 'builtin_chapters-show-tags';
    protected static string $view = 'chapters.parts.show-sidebar-section-tags';
    protected static string $labelTranslationKey = 'entities.tags';

    public function withData(array $viewData): array
    {
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        return [
            'chapter' => $chapter,
        ];
    }
}

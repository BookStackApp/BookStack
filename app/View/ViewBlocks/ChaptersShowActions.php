<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Chapter;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowActions extends ViewBlock
{
    protected static string $id = 'builtin_chapters-show-actions';
    protected static string $view = 'chapters.parts.show-sidebar-section-actions';
    protected static string $labelTranslationKey = 'common.actions';

    public function withData(array $viewData): array
    {
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        return [
            'chapter' => $chapter,
            'watchOptions' => new UserEntityWatchOptions(user(), $chapter),
        ];
    }
}

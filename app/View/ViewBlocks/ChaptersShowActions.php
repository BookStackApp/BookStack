<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Entities\Models\Chapter;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class ChaptersShowActions extends ViewBlock
{
    protected string $view = 'chapters.parts.show-sidebar-section-actions';

    public function withData(array $viewData, Request $request): array
    {
        /** @var Chapter $chapter */
        $chapter = $viewData['chapter'];

        return [
            'chapter' => $chapter,
            'watchOptions' => new UserEntityWatchOptions(user(), $chapter),
        ];
    }
}

<?php

namespace BookStack\View\ViewBlocks;

use BookStack\Entities\Models\Page;
use BookStack\View\ViewBlock;
use Illuminate\Http\Request;

class PagesShowAttachments extends ViewBlock
{
    protected static string $id = 'builtin_pages-show-attachments';
    protected static string $view = 'pages.parts.show-sidebar-section-attachments';
    protected static string $labelTranslationKey = 'entities.pages_attachments';

    public function withData(array $viewData): array
    {
        /** @var Page $page */
        $page = $viewData['page'];

        return [
            'page' => $page,
        ];
    }
}

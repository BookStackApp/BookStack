<?php

namespace BookStack\Exports;

use BookStack\App\Model;
use BookStack\Exports\ZipExportModels\ZipExportAttachment;
use BookStack\Exports\ZipExportModels\ZipExportPage;

class ZipExportReferences
{
    /** @var ZipExportPage[] */
    protected array $pages = [];
    protected array $books = [];
    protected array $chapters = [];

    /** @var ZipExportAttachment[] */
    protected array $attachments = [];

    public function __construct(
        protected ZipReferenceParser $parser,
    ) {
    }

    public function addPage(ZipExportPage $page): void
    {
        if ($page->id) {
            $this->pages[$page->id] = $page;
        }

        foreach ($page->attachments as $attachment) {
            if ($attachment->id) {
                $this->attachments[$attachment->id] = $attachment;
            }
        }
    }

    public function buildReferences(): void
    {
        // TODO - References to images, attachments, other entities

        // TODO - Parse page MD & HTML
        foreach ($this->pages as $page) {
            $page->html = $this->parser->parse($page->html ?? '', function (Model $model): ?string {
                // TODO - Handle found link to $model
                //   - Validate we can see/access $model, or/and that it's
                //     part of the export in progress.

                // TODO - Add images after the above to files
                return '[CAT]';
            });
            // TODO - markdown
        }

//        dd('end');
        // TODO - Parse chapter desc html
        // TODO - Parse book desc html
    }
}

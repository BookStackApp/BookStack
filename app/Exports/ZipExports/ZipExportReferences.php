<?php

namespace BookStack\Exports\ZipExports;

use BookStack\App\Model;
use BookStack\Exports\ZipExports\Models\ZipExportAttachment;
use BookStack\Exports\ZipExports\Models\ZipExportImage;
use BookStack\Exports\ZipExports\Models\ZipExportModel;
use BookStack\Exports\ZipExports\Models\ZipExportPage;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\Image;

class ZipExportReferences
{
    /** @var ZipExportPage[] */
    protected array $pages = [];
    protected array $books = [];
    protected array $chapters = [];

    /** @var ZipExportAttachment[] */
    protected array $attachments = [];

    /** @var ZipExportImage[] */
    protected array $images = [];

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

    public function buildReferences(ZipExportFiles $files): void
    {
        // Parse page content first
        foreach ($this->pages as $page) {
            $handler = function (Model $model) use ($files, $page) {
                return $this->handleModelReference($model, $page, $files);
            };

            $page->html = $this->parser->parse($page->html ?? '', $handler);
            if ($page->markdown) {
                $page->markdown = $this->parser->parse($page->markdown, $handler);
            }
        }

//        dd('end');
        // TODO - Parse chapter desc html
        // TODO - Parse book desc html
    }

    protected function handleModelReference(Model $model, ZipExportModel $exportModel, ZipExportFiles $files): ?string
    {
        // TODO - References to other entities

        // Handle attachment references
        // No permission check needed here since they would only already exist in this
        // reference context if already allowed via their entity access.
        if ($model instanceof Attachment) {
            if (isset($this->attachments[$model->id])) {
                return "[[bsexport:attachment:{$model->id}]]";
            }
            return null;
        }

        // Handle image references
        if ($model instanceof Image) {
            // Only handle gallery and drawio images
            if ($model->type !== 'gallery' && $model->type !== 'drawio') {
                return null;
            }

            // We don't expect images to be part of book/chapter content
            if (!($exportModel instanceof ZipExportPage)) {
                return null;
            }

            $page = $model->getPage();
            if ($page && userCan('view', $page)) {
                if (!isset($this->images[$model->id])) {
                    $exportImage = ZipExportImage::fromModel($model, $files);
                    $this->images[$model->id] = $exportImage;
                    $exportModel->images[] = $exportImage;
                }
                return "[[bsexport:image:{$model->id}]]";
            }
            return null;
        }

        return null;
    }
}
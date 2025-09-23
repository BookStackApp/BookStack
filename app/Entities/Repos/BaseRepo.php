<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityContainerData;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Exceptions\ImageUploadException;
use BookStack\References\ReferenceStore;
use BookStack\References\ReferenceUpdater;
use BookStack\Sorting\BookSorter;
use BookStack\Uploads\ImageRepo;
use BookStack\Util\HtmlDescriptionFilter;
use Illuminate\Http\UploadedFile;

class BaseRepo
{
    public function __construct(
        protected TagRepo $tagRepo,
        protected ImageRepo $imageRepo,
        protected ReferenceUpdater $referenceUpdater,
        protected ReferenceStore $referenceStore,
        protected PageQueries $pageQueries,
        protected BookSorter $bookSorter,
    ) {
    }

    /**
     * Create a new entity in the system.
     */
    public function create(Entity $entity, array $input): void
    {
        $entityInput = array_intersect_key($input, ['name', 'priority']);
        $entity->forceFill($entityInput);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by'   => user()->id,
        ]);
        $entity->refreshSlug();
        $entity->save();

        if ($entity->shouldHaveContainerData()) {
            $containerData = new EntityContainerData();
            $this->updateContainerDescription($containerData, $input);
            $entity->containerData()->save($containerData);
        }

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->refresh();
        $entity->rebuildPermissions();
        $entity->indexForSearch();

        $this->referenceStore->updateForEntity($entity);
    }

    /**
     * Update the given entity.
     */
    public function update(Entity $entity, array $input): void
    {
        $oldUrl = $entity->getUrl();

        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name') || empty($entity->slug)) {
            $entity->refreshSlug();
        }

        $entity->save();
        if ($entity->shouldHaveContainerData() && $entity->containerData) {
            $this->updateContainerDescription($entity->containerData, $input);
        }

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
            $entity->touch();
        }

        $entity->indexForSearch();
        $this->referenceStore->updateForEntity($entity);

        if ($oldUrl !== $entity->getUrl()) {
            $this->referenceUpdater->updateEntityReferences($entity, $oldUrl);
        }
    }

    /**
     * Update the given items' cover image or clear it.
     *
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function updateCoverImage(EntityContainerData $containerData, ?UploadedFile $coverImage, bool $removeImage = false): void
    {
        if ($coverImage) {
            $imageType = 'cover_' . $containerData->entity_type;
            $this->imageRepo->destroyImage($containerData->cover()->first());
            $image = $this->imageRepo->saveNew($coverImage, $imageType, $containerData->entity_id, 512, 512, true);
            $containerData->cover()->associate($image);
            $containerData->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($containerData->cover()->first());
            $containerData->cover()->dissociate();
            $containerData->save();
        }
    }

    /**
     * Update the default page template used for this item.
     * Checks that, if changing, the provided value is a valid template and the user
     * has visibility of the provided page template id.
     */
    public function updateDefaultTemplate(Book|Chapter $entity, int $templateId): void
    {
        $changing = $templateId !== intval($entity->default_template_id);
        if (!$changing) {
            return;
        }

        if ($templateId === 0) {
            $entity->default_template_id = null;
            $entity->save();
            return;
        }

        $templateExists = $this->pageQueries->visibleTemplates()
            ->where('id', '=', $templateId)
            ->exists();

        $entity->default_template_id = $templateExists ? $templateId : null;
        $entity->save();
    }

    /**
     * Sort the parent of the given entity, if any auto sort actions are set for it.
     * Typically ran during create/update/insert events.
     */
    public function sortParent(Entity $entity): void
    {
        if ($entity instanceof BookChild) {
            $book = $entity->book;
            $this->bookSorter->runBookAutoSort($book);
        }
    }

    protected function updateContainerDescription(EntityContainerData $data, array $input): void
    {
        if (isset($input['description_html'])) {
            $data->setDescriptionHtml(
                HtmlDescriptionFilter::filterFromString($input['description_html']),
                html_entity_decode(strip_tags($input['description_html']))
            );
        } else if (isset($input['description'])) {
            $data->setDescriptionHtml('', $input['description']);
        }
    }
}

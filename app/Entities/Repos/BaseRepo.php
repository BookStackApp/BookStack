<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\EntityContainerContents;
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
     * @template T of Entity
     * @param T $entity
     * @return T
     */
    public function create(Entity $entity, array $input): Entity
    {
        $entity = $entity->clone()->refresh();
        $entityInput = array_intersect_key($input, ['name', 'priority']);
        $entity->forceFill($entityInput);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by'   => user()->id,
        ]);
        $entity->refreshSlug();
        $entity->save();

        if ($entity->isContainer()) {
            $contents = $entity->contents();
            $this->updateContainerDescription($contents, $input);
            $contents->save();
        }

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->refresh();
        $entity->rebuildPermissions();
        $entity->indexForSearch();

        $this->referenceStore->updateForEntity($entity);

        return $entity;
    }

    /**
     * Update the given entity.
     * @template T of Entity
     * @param T $entity
     * @return T
     */
    public function update(Entity $entity, array $input): Entity
    {
        $entity = $entity->clone()->refresh();
        $oldUrl = $entity->getUrl();

        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name') || empty($entity->slug)) {
            $entity->refreshSlug();
        }

        $entity->save();
        if ($entity->isContainer()) {
            $this->updateContainerDescription($entity->contents(), $input);
            $entity->contents()->save();
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

        return $entity;
    }

    /**
     * Update the given items' cover image or clear it.
     *
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function updateCoverImage(EntityContainerContents $contents, ?UploadedFile $coverImage, bool $removeImage = false): void
    {
        if (!$contents->supportsCoverImage()) {
            return;
        }

        if ($coverImage) {
            $imageType = 'cover_' . $contents->entity_type;
            $this->imageRepo->destroyImage($contents->cover()->first());
            $image = $this->imageRepo->saveNew($coverImage, $imageType, $contents->entity_id, 512, 512, true);
            $contents->cover()->associate($image);
            $contents->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($contents->cover()->first());
            $contents->cover()->dissociate();
            $contents->save();
        }
    }

    /**
     * Update the default page template used for this item.
     * Checks that, if changing, the provided value is a valid template and the user
     * has visibility of the provided page template id.
     */
    public function updateDefaultTemplate(EntityContainerContents $contents, int $templateId): void
    {
        $changing = $templateId !== intval($contents->default_template_id);
        if (!$changing || !$contents->supportsDefaultTemplate()) {
            return;
        }

        if ($templateId === 0) {
            $contents->default_template_id = null;
            $contents->save();
            return;
        }

        $templateExists = $this->pageQueries->visibleTemplates()
            ->where('id', '=', $templateId)
            ->exists();

        $contents->default_template_id = $templateExists ? $templateId : null;
        $contents->save();
    }

    /**
     * Sort the parent of the given entity if any auto sort actions are set for it.
     * Typically ran during create/update/insert events.
     */
    public function sortParent(Entity $entity): void
    {
        if ($entity instanceof BookChild) {
            $book = $entity->book;
            $this->bookSorter->runBookAutoSort($book);
        }
    }

    /**
     * Update the description of the given container data from input data.
     */
    protected function updateContainerDescription(EntityContainerContents $contents, array $input): void
    {
        if (isset($input['description_html'])) {
            $contents->setDescriptionHtml(
                HtmlDescriptionFilter::filterFromString($input['description_html']),
                html_entity_decode(strip_tags($input['description_html']))
            );
        } else if (isset($input['description'])) {
            $contents->setDescriptionHtml('', $input['description']);
        }
    }
}

<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\HasCoverInterface;
use BookStack\Entities\Models\HasDescriptionInterface;
use BookStack\Entities\Models\Entity;
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
        $entity = (clone $entity)->refresh();
        $entity->fill($input);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by'   => user()->id,
        ]);
        $entity->refreshSlug();

        if ($entity instanceof HasDescriptionInterface) {
            $this->updateDescription($entity, $input);
        }

        $entity->save();

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
        $oldUrl = $entity->getUrl();

        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name') || empty($entity->slug)) {
            $entity->refreshSlug();
        }

        if ($entity instanceof HasDescriptionInterface) {
            $this->updateDescription($entity, $input);
        }

        $entity->save();

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
    public function updateCoverImage(Entity&HasCoverInterface $entity, ?UploadedFile $coverImage, bool $removeImage = false): void
    {
        if ($coverImage) {
            $imageType = 'cover_' . $entity->type;
            $this->imageRepo->destroyImage($entity->coverInfo()->getImage());
            $image = $this->imageRepo->saveNew($coverImage, $imageType, $entity->id, 512, 512, true);
            $entity->coverInfo()->setImage($image);
            $entity->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($entity->coverInfo()->getImage());
            $entity->coverInfo()->setImage(null);
            $entity->save();
        }
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
     * Update the description of the given entity from input data.
     */
    protected function updateDescription(Entity $entity, array $input): void
    {
        if (!$entity instanceof HasDescriptionInterface) {
            return;
        }

        if (isset($input['description_html'])) {
            $entity->descriptionInfo()->set(
                HtmlDescriptionFilter::filterFromString($input['description_html']),
                html_entity_decode(strip_tags($input['description_html']))
            );
        } else if (isset($input['description'])) {
            $entity->descriptionInfo()->set('', $input['description']);
        }
    }
}

<?php

namespace BookStack\Entities\Repos;

use BookStack\Activity\TagRepo;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\HasCoverImage;
use BookStack\Exceptions\ImageUploadException;
use BookStack\References\ReferenceUpdater;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\UploadedFile;

class BaseRepo
{
    protected TagRepo $tagRepo;
    protected ImageRepo $imageRepo;
    protected ReferenceUpdater $referenceUpdater;

    public function __construct(TagRepo $tagRepo, ImageRepo $imageRepo, ReferenceUpdater $referenceUpdater)
    {
        $this->tagRepo = $tagRepo;
        $this->imageRepo = $imageRepo;
        $this->referenceUpdater = $referenceUpdater;
    }

    /**
     * Create a new entity in the system.
     */
    public function create(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by'   => user()->id,
        ]);
        $entity->refreshSlug();
        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->refresh();
        $entity->rebuildPermissions();
        $entity->indexForSearch();
    }

    /**
     * Update the given entity.
     */
    public function update(Entity $entity, array $input)
    {
        $oldUrl = $entity->getUrl();

        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name') || empty($entity->slug)) {
            $entity->refreshSlug();
        }

        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
            $entity->touch();
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();

        if ($oldUrl !== $entity->getUrl()) {
            $this->referenceUpdater->updateEntityPageReferences($entity, $oldUrl);
        }
    }

    /**
     * Update the given items' cover image, or clear it.
     *
     * @param Entity&HasCoverImage $entity
     *
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function updateCoverImage($entity, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        if ($coverImage) {
            $imageType = $entity->coverImageTypeKey();
            $this->imageRepo->destroyImage($entity->cover()->first());
            $image = $this->imageRepo->saveNew($coverImage, $imageType, $entity->id, 512, 512, true);
            $entity->cover()->associate($image);
            $entity->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($entity->cover()->first());
            $entity->image_id = 0;
            $entity->save();
        }
    }
}

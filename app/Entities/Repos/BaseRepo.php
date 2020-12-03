<?php

namespace BookStack\Entities\Repos;

use BookStack\Actions\ActivityType;
use BookStack\Actions\TagRepo;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\HasCoverImage;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Facades\Activity;
use BookStack\Uploads\ImageRepo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BaseRepo
{

    protected $tagRepo;
    protected $imageRepo;


    public function __construct(TagRepo $tagRepo, ImageRepo $imageRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Create a new entity in the system
     */
    public function create(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
        ]);
        $entity->refreshSlug();
        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();
    }

    /**
     * Update the given entity.
     */
    public function update(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name')) {
            $entity->refreshSlug();
        }

        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();
    }

    /**
     * Update the given items' cover image, or clear it.
     * @throws ImageUploadException
     * @throws \Exception
     */
    public function updateCoverImage(HasCoverImage $entity, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        if ($coverImage) {
            $this->imageRepo->destroyImage($entity->cover);
            $image = $this->imageRepo->saveNew($coverImage, 'cover_book', $entity->id, 512, 512, true);
            $entity->cover()->associate($image);
            $entity->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($entity->cover);
            $entity->image_id = 0;
            $entity->save();
        }
    }

    /**
     * Update the permissions of an entity.
     */
    public function updatePermissions(Entity $entity, bool $restricted, Collection $permissions = null)
    {
        $entity->restricted = $restricted;
        $entity->permissions()->delete();

        if (!is_null($permissions)) {
            $entityPermissionData = $permissions->flatMap(function ($restrictions, $roleId) {
                return collect($restrictions)->keys()->map(function ($action) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'action' => strtolower($action),
                    ] ;
                });
            });

            $entity->permissions()->createMany($entityPermissionData);
        }

        $entity->save();
        $entity->rebuildPermissions();
        Activity::addForEntity($entity, ActivityType::PERMISSIONS_UPDATE);
    }
}

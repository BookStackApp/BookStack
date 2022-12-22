<?php

namespace BookStack\Auth\Permissions;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

/**
 * Joint permissions provide a pre-query "cached" table of view permissions for all core entity
 * types for all roles in the system. This class generates out that table for different scenarios.
 */
class JointPermissionBuilder
{
    /**
     * Re-generate all entity permission from scratch.
     */
    public function rebuildForAll()
    {
        DB::table('entity_permissions_collapsed')->truncate();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function (EloquentCollection $books) {
            $this->buildJointPermissionsForBooks($books);
        });

        // Chunk through all bookshelves
        Bookshelf::query()->withTrashed()
            ->select(['id', 'owned_by'])
            ->chunk(50, function (EloquentCollection $shelves) {
                $this->generateCollapsedPermissions($shelves->all());
            });
    }

    /**
     * Rebuild the entity jointPermissions for a particular entity.
     */
    public function rebuildForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity instanceof Book) {
            $books = $this->bookFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildJointPermissionsForBooks($books, true);

            return;
        }

        /** @var BookChild $entity */
        if ($entity->book) {
            $entities[] = $entity->book;
        }

        if ($entity instanceof Page && $entity->chapter_id) {
            $entities[] = $entity->chapter;
        }

        if ($entity instanceof Chapter) {
            foreach ($entity->pages as $page) {
                $entities[] = $page;
            }
        }

        $this->buildJointPermissionsForEntities($entities);
    }

    /**
     * Get a query for fetching a book with its children.
     */
    protected function bookFetchQuery(): Builder
    {
        return Book::query()->withTrashed()
            ->select(['id', 'owned_by'])->with([
                'chapters' => function ($query) {
                    $query->withTrashed()->select(['id', 'owned_by', 'book_id']);
                },
                'pages' => function ($query) {
                    $query->withTrashed()->select(['id', 'owned_by', 'book_id', 'chapter_id']);
                },
            ]);
    }

    /**
     * Build joint permissions for the given book and role combinations.
     */
    protected function buildJointPermissionsForBooks(EloquentCollection $books, bool $deleteOld = false)
    {
        $entities = clone $books;

        /** @var Book $book */
        foreach ($books->all() as $book) {
            foreach ($book->getRelation('chapters') as $chapter) {
                $entities->push($chapter);
            }
            foreach ($book->getRelation('pages') as $page) {
                $entities->push($page);
            }
        }

        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($entities->all());
        }

        $this->generateCollapsedPermissions($entities->all());
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     */
    protected function buildJointPermissionsForEntities(array $entities)
    {
        $this->deleteManyJointPermissionsForEntities($entities);
        $this->generateCollapsedPermissions($entities);
    }

    /**
     * Delete all the entity jointPermissions for a list of entities.
     *
     * @param Entity[] $entities
     */
    protected function deleteManyJointPermissionsForEntities(array $entities)
    {
        $simpleEntities = $this->entitiesToSimpleEntities($entities);
        $idsByType = $this->entitiesToTypeIdMap($simpleEntities);

        DB::transaction(function () use ($idsByType) {
            foreach ($idsByType as $type => $ids) {
                foreach (array_chunk($ids, 1000) as $idChunk) {
                    DB::table('entity_permissions_collapsed')
                        ->where('entity_type', '=', $type)
                        ->whereIn('entity_id', $idChunk)
                        ->delete();
                }
            }
        });
    }

    /**
     * @param Entity[] $entities
     *
     * @return SimpleEntityData[]
     */
    protected function entitiesToSimpleEntities(array $entities): array
    {
        $simpleEntities = [];

        foreach ($entities as $entity) {
            $attrs = $entity->getAttributes();
            $simple = new SimpleEntityData();
            $simple->id = $attrs['id'];
            $simple->type = $entity->getMorphClass();
            $simple->owned_by = $attrs['owned_by'] ?? 0;
            $simple->book_id = $attrs['book_id'] ?? null;
            $simple->chapter_id = $attrs['chapter_id'] ?? null;
            $simpleEntities[] = $simple;
        }

        return $simpleEntities;
    }

    /**
     * Create & Save collapsed entity permissions.
     *
     * @param Entity[] $originalEntities
     */
    protected function generateCollapsedPermissions(array $originalEntities)
    {
        $entities = $this->entitiesToSimpleEntities($originalEntities);
        $jointPermissions = [];

        // Fetch related entity permissions
        $permissions = $this->getEntityPermissionsForEntities($entities);

        // Create a mapping of explicit entity permissions
        $permissionMap = new EntityPermissionMap($permissions);

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            array_push($jointPermissions, ...$this->createCollapsedPermissionData($entity, $permissionMap));
        }

        DB::transaction(function () use ($jointPermissions) {
            foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                DB::table('entity_permissions_collapsed')->insert($jointPermissionChunk);
            }
        });
    }

    /**
     * Create collapsed permission data for the given entity using the given permission map.
     */
    protected function createCollapsedPermissionData(SimpleEntityData $entity, EntityPermissionMap $permissionMap): array
    {
        $chain = [
            $entity->type . ':' . $entity->id,
            $entity->chapter_id ? null : ('chapter:' . $entity->chapter_id),
            $entity->book_id ? null : ('book:' . $entity->book_id),
        ];

        $permissionData = [];
        $overridesApplied = [];

        foreach ($chain as $entityTypeId) {
            if ($entityTypeId === null) {
                continue;
            }

            $permissions = $permissionMap->getForEntity($entityTypeId);
            foreach ($permissions as $permission) {
                $related = $permission->getAssignedType() . ':' . $permission->getAssignedTypeId();
                if (!isset($overridesApplied[$related])) {
                    $permissionData[] = [
                        'role_id' => $permission->role_id,
                        'user_id' => $permission->user_id,
                        'view' => $permission->view,
                        'entity_type' => $entity->type,
                        'entity_id' => $entity->id,
                    ];
                    $overridesApplied[$related] = true;
                }
            }
        }

        return $permissionData;
    }

    /**
     * From the given entity list, provide back a mapping of entity types to
     * the ids of that given type. The type used is the DB morph class.
     *
     * @param SimpleEntityData[] $entities
     *
     * @return array<string, int[]>
     */
    protected function entitiesToTypeIdMap(array $entities): array
    {
        $idsByType = [];

        foreach ($entities as $entity) {
            if (!isset($idsByType[$entity->type])) {
                $idsByType[$entity->type] = [];
            }

            $idsByType[$entity->type][] = $entity->id;
        }

        return $idsByType;
    }

    /**
     * Get the entity permissions for all the given entities.
     *
     * @param SimpleEntityData[] $entities
     *
     * @return EntityPermission[]
     */
    protected function getEntityPermissionsForEntities(array $entities): array
    {
        $idsByType = $this->entitiesToTypeIdMap($entities);
        $permissionFetch = EntityPermission::query()
            ->where(function (Builder $query) use ($idsByType) {
                foreach ($idsByType as $type => $ids) {
                    $query->orWhere(function (Builder $query) use ($type, $ids) {
                        $query->where('entity_type', '=', $type)->whereIn('entity_id', $ids);
                    });
                }
            });

        return $permissionFetch->get()->all();
    }
}

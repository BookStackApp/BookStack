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
 * Collapsed permissions act as a "flattened" view of entity-level permissions in the system
 * so inheritance does not have to managed as part of permission querying.
 */
class CollapsedPermissionBuilder
{
    /**
     * Re-generate all collapsed permissions from scratch.
     */
    public function rebuildForAll()
    {
        DB::table('entity_permissions_collapsed')->truncate();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function (EloquentCollection $books) {
            $this->buildForBooks($books, false);
        });

        // Chunk through all bookshelves
        Bookshelf::query()->withTrashed()
            ->select(['id'])
            ->chunk(50, function (EloquentCollection $shelves) {
                $this->generateCollapsedPermissions($shelves->all());
            });
    }

    /**
     * Rebuild the collapsed permissions for a particular entity.
     */
    public function rebuildForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity instanceof Book) {
            $books = $this->bookFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildForBooks($books, true);

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

        $this->buildForEntities($entities);
    }

    /**
     * Get a query for fetching a book with its children.
     */
    protected function bookFetchQuery(): Builder
    {
        return Book::query()->withTrashed()
            ->select(['id'])->with([
                'chapters' => function ($query) {
                    $query->withTrashed()->select(['id', 'book_id']);
                },
                'pages' => function ($query) {
                    $query->withTrashed()->select(['id', 'book_id', 'chapter_id']);
                },
            ]);
    }

    /**
     * Build collapsed permissions for the given books.
     */
    protected function buildForBooks(EloquentCollection $books, bool $deleteOld)
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
            $this->deleteForEntities($entities->all());
        }

        $this->generateCollapsedPermissions($entities->all());
    }

    /**
     * Rebuild the collapsed permissions for a collection of entities.
     */
    protected function buildForEntities(array $entities)
    {
        $this->deleteForEntities($entities);
        $this->generateCollapsedPermissions($entities);
    }

    /**
     * Delete the stored collapsed permissions for a list of entities.
     *
     * @param Entity[] $entities
     */
    protected function deleteForEntities(array $entities)
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
     * Convert the given list of entities into "SimpleEntityData" representations
     * for faster usage and property access.
     *
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
        $collapsedPermData = [];

        // Fetch related entity permissions
        $permissions = $this->getEntityPermissionsForEntities($entities);

        // Create a mapping of explicit entity permissions
        $permissionMap = new EntityPermissionMap($permissions);

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            array_push($collapsedPermData, ...$this->createCollapsedPermissionData($entity, $permissionMap));
        }

        DB::transaction(function () use ($collapsedPermData) {
            foreach (array_chunk($collapsedPermData, 1000) as $dataChunk) {
                DB::table('entity_permissions_collapsed')->insert($dataChunk);
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
            $entity->chapter_id ? ('chapter:' . $entity->chapter_id) : null,
            $entity->book_id ? ('book:' . $entity->book_id) : null,
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

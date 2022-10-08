<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
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
     * @var array<string, array<int, SimpleEntityData>>
     */
    protected $entityCache;

    /**
     * Re-generate all entity permission from scratch.
     */
    public function rebuildForAll()
    {
        JointPermission::query()->truncate();

        // Get all roles (Should be the most limited dimension)
        $roles = Role::query()->with('permissions')->get()->all();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function (EloquentCollection $books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });

        // Chunk through all bookshelves
        Bookshelf::query()->withTrashed()->select(['id', 'restricted', 'owned_by'])
            ->chunk(50, function (EloquentCollection $shelves) use ($roles) {
                $this->createManyJointPermissions($shelves->all(), $roles);
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
            $this->buildJointPermissionsForBooks($books, Role::query()->with('permissions')->get()->all(), true);

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
     * Build the entity jointPermissions for a particular role.
     */
    public function rebuildForRole(Role $role)
    {
        $roles = [$role];
        $role->jointPermissions()->delete();
        $role->load('permissions');

        // Chunk through all books
        $this->bookFetchQuery()->chunk(20, function ($books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });

        // Chunk through all bookshelves
        Bookshelf::query()->select(['id', 'restricted', 'owned_by'])
            ->chunk(50, function ($shelves) use ($roles) {
                $this->createManyJointPermissions($shelves->all(), $roles);
            });
    }

    /**
     * Prepare the local entity cache and ensure it's empty.
     *
     * @param SimpleEntityData[] $entities
     */
    protected function readyEntityCache(array $entities)
    {
        $this->entityCache = [];

        foreach ($entities as $entity) {
            if (!isset($this->entityCache[$entity->type])) {
                $this->entityCache[$entity->type] = [];
            }

            $this->entityCache[$entity->type][$entity->id] = $entity;
        }
    }

    /**
     * Get a book via ID, Checks local cache.
     */
    protected function getBook(int $bookId): SimpleEntityData
    {
        return $this->entityCache['book'][$bookId];
    }

    /**
     * Get a chapter via ID, Checks local cache.
     */
    protected function getChapter(int $chapterId): SimpleEntityData
    {
        return $this->entityCache['chapter'][$chapterId];
    }

    /**
     * Get a query for fetching a book with its children.
     */
    protected function bookFetchQuery(): Builder
    {
        return Book::query()->withTrashed()
            ->select(['id', 'restricted', 'owned_by'])->with([
                'chapters' => function ($query) {
                    $query->withTrashed()->select(['id', 'restricted', 'owned_by', 'book_id']);
                },
                'pages' => function ($query) {
                    $query->withTrashed()->select(['id', 'restricted', 'owned_by', 'book_id', 'chapter_id']);
                },
            ]);
    }

    /**
     * Build joint permissions for the given book and role combinations.
     */
    protected function buildJointPermissionsForBooks(EloquentCollection $books, array $roles, bool $deleteOld = false)
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

        $this->createManyJointPermissions($entities->all(), $roles);
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     */
    protected function buildJointPermissionsForEntities(array $entities)
    {
        $roles = Role::query()->get()->values()->all();
        $this->deleteManyJointPermissionsForEntities($entities);
        $this->createManyJointPermissions($entities, $roles);
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
                    DB::table('joint_permissions')
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
            $simple->restricted = boolval($attrs['restricted'] ?? 0);
            $simple->owned_by = $attrs['owned_by'] ?? 0;
            $simple->book_id = $attrs['book_id'] ?? null;
            $simple->chapter_id = $attrs['chapter_id'] ?? null;
            $simpleEntities[] = $simple;
        }

        return $simpleEntities;
    }

    /**
     * Create & Save entity jointPermissions for many entities and roles.
     *
     * @param Entity[] $entities
     * @param Role[]   $roles
     */
    protected function createManyJointPermissions(array $originalEntities, array $roles)
    {
        $entities = $this->entitiesToSimpleEntities($originalEntities);
        $this->readyEntityCache($entities);
        $jointPermissions = [];

        // Create a mapping of entity restricted statuses
        $entityRestrictedMap = [];
        foreach ($entities as $entity) {
            $entityRestrictedMap[$entity->type . ':' . $entity->id] = $entity->restricted;
        }

        // Fetch related entity permissions
        $permissions = $this->getEntityPermissionsForEntities($entities);

        // Create a mapping of explicit entity permissions
        // TODO - Handle new format, Now getting all defined entity permissions
        //   from the above call, Need to handle entries with none, and the 'Other Roles' (role_id=0)
        //   fallback option.
        $permissionMap = [];
        foreach ($permissions as $permission) {
            $key = $permission->entity_type . ':' . $permission->entity_id . ':' . $permission->role_id;
            $isRestricted = $entityRestrictedMap[$permission->entity_type . ':' . $permission->entity_id];
            $permissionMap[$key] = $isRestricted;
        }

        // Create a mapping of role permissions
        $rolePermissionMap = [];
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $rolePermissionMap[$role->getRawAttribute('id') . ':' . $permission->getRawAttribute('name')] = true;
            }
        }

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                $jointPermissions[] = $this->createJointPermissionData(
                    $entity,
                    $role->getRawAttribute('id'),
                    $permissionMap,
                    $rolePermissionMap,
                    $role->system_name === 'admin'
                );
            }
        }

        DB::transaction(function () use ($jointPermissions) {
            foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                DB::table('joint_permissions')->insert($jointPermissionChunk);
            }
        });
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

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     */
    protected function createJointPermissionData(SimpleEntityData $entity, int $roleId, array $permissionMap, array $rolePermissionMap, bool $isAdminRole): array
    {
        $permissionPrefix = $entity->type . '-view';
        $roleHasPermission = isset($rolePermissionMap[$roleId . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$roleId . ':' . $permissionPrefix . '-own']);

        if ($isAdminRole) {
            return $this->createJointPermissionDataArray($entity, $roleId, true, true);
        }

        if ($entity->restricted) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $roleId);

            return $this->createJointPermissionDataArray($entity, $roleId, $hasAccess, $hasAccess);
        }

        if ($entity->type === 'book' || $entity->type === 'bookshelf') {
            return $this->createJointPermissionDataArray($entity, $roleId, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Book.
        $book = $this->getBook($entity->book_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $book, $roleId);
        $hasPermissiveAccessToParents = !$book->restricted;

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
        if ($entity->type === 'page' && $entity->chapter_id !== 0) {
            $chapter = $this->getChapter($entity->chapter_id);
            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapter->restricted;
            if ($chapter->restricted) {
                $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $chapter, $roleId);
            }
        }

        return $this->createJointPermissionDataArray(
            $entity,
            $roleId,
            ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
            ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
        );
    }

    /**
     * Check for an active restriction in an entity map.
     */
    protected function mapHasActiveRestriction(array $entityMap, SimpleEntityData $entity, int $roleId): bool
    {
        $key = $entity->type . ':' . $entity->id . ':' . $roleId;

        return $entityMap[$key] ?? false;
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     */
    protected function createJointPermissionDataArray(SimpleEntityData $entity, int $roleId, bool $permissionAll, bool $permissionOwn): array
    {
        return [
            'entity_id'          => $entity->id,
            'entity_type'        => $entity->type,
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'owned_by'           => $entity->owned_by,
            'role_id'            => $roleId,
        ];
    }
}

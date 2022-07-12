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

class JointPermissionBuilder
{
    /**
     * @var array<string, array<int, Entity>>
     */
    protected $entityCache;

    /**
     * Re-generate all entity permission from scratch.
     */
    public function rebuildForAll()
    {
        JointPermission::query()->truncate();
        $this->readyEntityCache();

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
     *
     * @throws Throwable
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
     * @param Entity[] $entities
     */
    protected function readyEntityCache(array $entities = [])
    {
        $this->entityCache = [];

        foreach ($entities as $entity) {
            $class = get_class($entity);

            if (!isset($this->entityCache[$class])) {
                $this->entityCache[$class] = [];
            }

            $this->entityCache[$class][$entity->getRawAttribute('id')] = $entity;
        }
    }

    /**
     * Get a book via ID, Checks local cache.
     */
    protected function getBook(int $bookId): ?Book
    {
        if ($this->entityCache[Book::class][$bookId] ?? false) {
            return $this->entityCache[Book::class][$bookId];
        }

        return Book::query()->withTrashed()->find($bookId);
    }

    /**
     * Get a chapter via ID, Checks local cache.
     */
    protected function getChapter(int $chapterId): ?Chapter
    {
        if ($this->entityCache[Chapter::class][$chapterId] ?? false) {
            return $this->entityCache[Chapter::class][$chapterId];
        }

        return Chapter::query()
            ->withTrashed()
            ->find($chapterId);
    }

    /**
     * Get a query for fetching a book with it's children.
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
     *
     * @throws Throwable
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
     *
     * @throws Throwable
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
     *
     * @throws Throwable
     */
    protected function deleteManyJointPermissionsForEntities(array $entities)
    {
        $idsByType = $this->entitiesToTypeIdMap($entities);

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
     * Create & Save entity jointPermissions for many entities and roles.
     *
     * @param Entity[] $entities
     * @param Role[]   $roles
     *
     * @throws Throwable
     */
    protected function createManyJointPermissions(array $entities, array $roles)
    {
        $this->readyEntityCache($entities);
        $jointPermissions = [];

        // Create a mapping of entity restricted statuses
        $entityRestrictedMap = [];
        foreach ($entities as $entity) {
            $entityRestrictedMap[$entity->getMorphClass() . ':' . $entity->getRawAttribute('id')] = boolval($entity->getRawAttribute('restricted'));
        }

        // Fetch related entity permissions
        $permissions = $this->getEntityPermissionsForEntities($entities);

        // Create a mapping of explicit entity permissions
        $permissionMap = [];
        foreach ($permissions as $permission) {
            $key = $permission->restrictable_type . ':' . $permission->restrictable_id . ':' . $permission->role_id . ':' . $permission->action;
            $isRestricted = $entityRestrictedMap[$permission->restrictable_type . ':' . $permission->restrictable_id];
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
                foreach ($this->getActions($entity) as $action) {
                    $jointPermissions[] = $this->createJointPermissionData($entity, $role, $action, $permissionMap, $rolePermissionMap);
                }
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
     * @param Entity[] $entities
     * @return array<string, int[]>
     */
    protected function entitiesToTypeIdMap(array $entities): array
    {
        $idsByType = [];

        foreach ($entities as $entity) {
            $type = $entity->getMorphClass();

            if (!isset($idsByType[$type])) {
                $idsByType[$type] = [];
            }

            $idsByType[$type][] = $entity->getRawAttribute('id');
        }

        return $idsByType;
    }

    /**
     * Get the entity permissions for all the given entities
     * @param Entity[] $entities
     * @return EloquentCollection
     */
    protected function getEntityPermissionsForEntities(array $entities)
    {
        $idsByType = $this->entitiesToTypeIdMap($entities);
        $permissionFetch = EntityPermission::query();

        foreach ($idsByType as $type => $ids) {
            $permissionFetch->orWhere(function (Builder $query) use ($type, $ids) {
                $query->where('restrictable_type', '=', $type)->whereIn('restrictable_id', $ids);
            });
        }

        return $permissionFetch->get();
    }

    /**
     * Get the actions related to an entity.
     */
    protected function getActions(Entity $entity): array
    {
        $baseActions = ['view', 'update', 'delete'];
        if ($entity instanceof Chapter || $entity instanceof Book) {
            $baseActions[] = 'page-create';
        }
        if ($entity instanceof Book) {
            $baseActions[] = 'chapter-create';
        }

        return $baseActions;
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     */
    protected function createJointPermissionData(Entity $entity, Role $role, string $action, array $permissionMap, array $rolePermissionMap): array
    {
        $permissionPrefix = (strpos($action, '-') === false ? ($entity->getType() . '-') : '') . $action;
        $roleHasPermission = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-own']);
        $explodedAction = explode('-', $action);
        $restrictionAction = end($explodedAction);

        if ($role->system_name === 'admin') {
            return $this->createJointPermissionDataArray($entity, $role, $action, true, true);
        }

        if ($entity->restricted) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $role, $restrictionAction);

            return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
        }

        if ($entity instanceof Book || $entity instanceof Bookshelf) {
            return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Book.
        $book = $this->getBook($entity->book_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $book, $role, $restrictionAction);
        $hasPermissiveAccessToParents = !$book->restricted;

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
        if ($entity instanceof Page && intval($entity->chapter_id) !== 0) {
            $chapter = $this->getChapter($entity->chapter_id);
            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapter->restricted;
            if ($chapter->restricted) {
                $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $chapter, $role, $restrictionAction);
            }
        }

        return $this->createJointPermissionDataArray(
            $entity,
            $role,
            $action,
            ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
            ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
        );
    }

    /**
     * Check for an active restriction in an entity map.
     */
    protected function mapHasActiveRestriction(array $entityMap, Entity $entity, Role $role, string $action): bool
    {
        $key = $entity->getMorphClass() . ':' . $entity->getRawAttribute('id') . ':' . $role->getRawAttribute('id') . ':' . $action;

        return $entityMap[$key] ?? false;
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     */
    protected function createJointPermissionDataArray(Entity $entity, Role $role, string $action, bool $permissionAll, bool $permissionOwn): array
    {
        return [
            'action'             => $action,
            'entity_id'          => $entity->getRawAttribute('id'),
            'entity_type'        => $entity->getMorphClass(),
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'owned_by'           => $entity->getRawAttribute('owned_by'),
            'role_id'            => $role->getRawAttribute('id'),
        ];
    }

}
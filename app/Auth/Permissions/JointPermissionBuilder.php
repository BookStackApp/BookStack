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
    protected array $entityCache;

    /**
     * Re-generate all entity permission from scratch.
     */
    public function rebuildForAll()
    {
        JointPermission::query()->truncate();
        JointUserPermission::query()->truncate();

        // Get all roles (Should be the most limited dimension)
        $roles = Role::query()->with('permissions')->get()->all();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function (EloquentCollection $books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });

        // Chunk through all bookshelves
        Bookshelf::query()->withTrashed()->select(['id', 'owned_by'])
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
        Bookshelf::query()->select(['id', 'owned_by'])
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
                    DB::table('joint_user_permissions')
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
     * Create & Save entity jointPermissions for many entities and roles.
     *
     * @param Entity[] $originalEntities
     * @param Role[]   $roles
     */
    protected function createManyJointPermissions(array $originalEntities, array $roles)
    {
        $entities = $this->entitiesToSimpleEntities($originalEntities);
        $this->readyEntityCache($entities);
        $jointPermissions = [];
        $jointUserPermissions = [];

        // Fetch related entity permissions
        $permissions = $this->getEntityPermissionsForEntities($entities);

        // Create a mapping of explicit entity permissions
        $permissionMap = [];
        $controlledUserIds = [];
        foreach ($permissions as $permission) {
            $type = $permission->role_id ? 'role' : ($permission->user_id ? 'user' : 'fallback');
            $id = $permission->role_id ?? $permission->user_id ?? '0';
            $key = $permission->entity_type . ':' . $permission->entity_id . ':' . $type  . ':' .  $id;
            if ($type === 'user') {
                $controlledUserIds[$id] = true;
            }
            $permissionMap[$key] = $permission->view;
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
            foreach ($controlledUserIds as $userId => $exists) {
                $userPermitted = $this->getUserPermissionOverrideStatus($entity, $userId, $permissionMap);
                if ($userPermitted !== null) {
                    $jointUserPermissions[] = $this->createJointUserPermissionDataArray($entity, $userId, $userPermitted);
                }
            }
        }

        DB::transaction(function () use ($jointPermissions) {
            foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                DB::table('joint_permissions')->insert($jointPermissionChunk);
            }
        });

        DB::transaction(function () use ($jointUserPermissions) {
            foreach (array_chunk($jointUserPermissions, 1000) as $jointUserPermissionsChunk) {
                DB::table('joint_user_permissions')->insert($jointUserPermissionsChunk);
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

        if ($this->entityPermissionsActiveForRole($permissionMap, $entity, $roleId)) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $roleId);

            return $this->createJointPermissionDataArray($entity, $roleId, $hasAccess, $hasAccess);
        }

        if ($entity->type === 'book' || $entity->type === 'bookshelf') {
            return $this->createJointPermissionDataArray($entity, $roleId, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Book.
        $book = $this->getBook($entity->book_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $book, $roleId);
        $hasPermissiveAccessToParents = !$this->entityPermissionsActiveForRole($permissionMap, $book, $roleId);

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
        if ($entity->type === 'page' && $entity->chapter_id !== 0) {
            $chapter = $this->getChapter($entity->chapter_id);
            $chapterRestricted = $this->entityPermissionsActiveForRole($permissionMap, $chapter, $roleId);
            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapterRestricted;
            if ($chapterRestricted) {
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
     * Get the status of a user-specific permission override for the given entity user combo if existing.
     * This can return null where no user-specific permission overrides are applicable.
     */
    protected function getUserPermissionOverrideStatus(SimpleEntityData $entity, int $userId, array $permissionMap): ?bool
    {
        // If direct permissions exists, return those
        $directKey = $entity->type . ':' . $entity->id . ':user:' . $userId;
        if (isset($permissionMap[$directKey])) {
            return $permissionMap[$directKey];
        }

        // If a book or shelf, exit out since no parents to check
        if ($entity->type === 'book' || $entity->type === 'bookshelf') {
            return null;
        }

        // If a chapter or page, get the parent book permission status.
        // defaults to null where no permission is set.
        $bookKey = 'book:' . $entity->book_id . ':user:' . $userId;
        $bookPermission = $permissionMap[$bookKey] ?? null;

        // If a page within a chapter, return the chapter permission if existing otherwise
        // default ot the parent book permission.
        if ($entity->type === 'page' && $entity->chapter_id !== 0) {
            $chapterKey = 'chapter:' . $entity->chapter_id . ':user:' . $userId;
            $chapterPermission = $permissionMap[$chapterKey] ?? null;
            return $chapterPermission ?? $bookPermission;
        }

        // Return the book permission status
        return $bookPermission;
    }

    /**
     * Check if entity permissions are defined within the given map, for the given entity and role.
     * Checks for the default `role_id=0` backup option as a fallback.
     */
    protected function entityPermissionsActiveForRole(array $permissionMap, SimpleEntityData $entity, int $roleId): bool
    {
        $keyPrefix = $entity->type . ':' . $entity->id . ':';
        return isset($permissionMap[$keyPrefix . 'role:' . $roleId]) || isset($permissionMap[$keyPrefix . 'fallback:0']);
    }

    /**
     * Check for an active restriction in an entity map.
     */
    protected function mapHasActiveRestriction(array $permissionMap, SimpleEntityData $entity, int $roleId): bool
    {
        $roleKey = $entity->type . ':' . $entity->id . ':role:' . $roleId;
        $defaultKey = $entity->type . ':' . $entity->id . ':fallback:0';

        return $permissionMap[$roleKey] ?? $permissionMap[$defaultKey] ?? false;
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

    /**
     * Create an array of data with the information of an JointUserPermission.
     * Used to build data for bulk insertion.
     */
    protected function createJointUserPermissionDataArray(SimpleEntityData $entity, int $userId, bool $hasPermission): array
    {
        return [
            'entity_id'          => $entity->id,
            'entity_type'        => $entity->type,
            'has_permission'     => $hasPermission,
            'user_id'            => $userId,
        ];
    }
}

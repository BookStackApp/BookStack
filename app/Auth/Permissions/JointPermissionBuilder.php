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
            $simple = SimpleEntityData::fromEntity($entity);
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
        $jointPermissions = [];

        // Fetch related entity permissions
        $permissions = new MassEntityPermissionEvaluator($entities, 'view');

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
                $jp = $this->createJointPermissionData(
                    $entity,
                    $role->getRawAttribute('id'),
                    $permissions,
                    $rolePermissionMap,
                    $role->system_name === 'admin'
                );
                $jointPermissions[] = $jp;
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
     * Create entity permission data for an entity and role
     * for a particular action.
     */
    protected function createJointPermissionData(SimpleEntityData $entity, int $roleId, MassEntityPermissionEvaluator $permissionMap, array $rolePermissionMap, bool $isAdminRole): array
    {
        // Ensure system admin role retains permissions
        if ($isAdminRole) {
            return $this->createJointPermissionDataArray($entity, $roleId, PermissionStatus::EXPLICIT_ALLOW, true);
        }

        // Return evaluated entity permission status if it has an affect.
        $entityPermissionStatus = $permissionMap->evaluateEntityForRole($entity, $roleId);
        if ($entityPermissionStatus !== null) {
            return $this->createJointPermissionDataArray($entity, $roleId, $entityPermissionStatus, false);
        }

        // Otherwise default to the role-level permissions
        $permissionPrefix = $entity->type . '-view';
        $roleHasPermission = isset($rolePermissionMap[$roleId . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$roleId . ':' . $permissionPrefix . '-own']);
        $status = $roleHasPermission ? PermissionStatus::IMPLICIT_ALLOW : PermissionStatus::IMPLICIT_DENY;
        return $this->createJointPermissionDataArray($entity, $roleId, $status, $roleHasPermissionOwn);
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     */
    protected function createJointPermissionDataArray(SimpleEntityData $entity, int $roleId, int $permissionStatus, bool $hasPermissionOwn): array
    {
        $ownPermissionActive = ($hasPermissionOwn && $permissionStatus !== PermissionStatus::EXPLICIT_DENY && $entity->owned_by);

        return [
            'entity_id'   => $entity->id,
            'entity_type' => $entity->type,
            'role_id'     => $roleId,
            'status'      => $permissionStatus,
            'owner_id'    => $ownPermissionActive ? $entity->owned_by : null,
        ];
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove entity-permissions on non-restricted entities
        $deleteInactiveEntityPermissions = function (string $table, string $morphClass) {
            $permissionIds = DB::table('entity_permissions')->select('entity_permissions.id as id')
                ->join($table, function (JoinClause $join) use ($table, $morphClass) {
                    return $join->where($table . '.restricted', '=', 0)
                        ->on($table . '.id', '=', 'entity_permissions.entity_id');
                })->where('entity_type', '=', $morphClass)
                ->pluck('id');
            DB::table('entity_permissions')->whereIn('id', $permissionIds)->delete();
        };
        $deleteInactiveEntityPermissions('pages', 'page');
        $deleteInactiveEntityPermissions('chapters', 'chapter');
        $deleteInactiveEntityPermissions('books', 'book');
        $deleteInactiveEntityPermissions('bookshelves', 'bookshelf');

        // Migrate restricted=1 entries to new entity_permissions (role_id=0) entries
        $defaultEntityPermissionGenQuery = function (Builder $query, string $table, string $morphClass) {
            return $query->select(['id as entity_id'])
                ->selectRaw('? as entity_type', [$morphClass])
                ->selectRaw('? as `role_id`', [0])
                ->selectRaw('? as `view`', [0])
                ->selectRaw('? as `create`', [0])
                ->selectRaw('? as `update`', [0])
                ->selectRaw('? as `delete`', [0])
                ->from($table)
                ->where('restricted', '=', 1);
        };

        $query = $defaultEntityPermissionGenQuery(DB::query(), 'pages', 'page')
            ->union(fn(Builder $query) => $defaultEntityPermissionGenQuery($query, 'books', 'book'))
            ->union(fn(Builder $query) => $defaultEntityPermissionGenQuery($query, 'chapters', 'chapter'))
            ->union(fn(Builder $query) => $defaultEntityPermissionGenQuery($query, 'bookshelves', 'bookshelf'));

        DB::table('entity_permissions')->insertUsing(['entity_id', 'entity_type', 'role_id', 'view', 'create', 'update', 'delete'], $query);

        // Drop restricted columns
        $dropRestrictedColumn = fn(Blueprint $table) => $table->dropColumn('restricted');
        Schema::table('pages', $dropRestrictedColumn);
        Schema::table('chapters', $dropRestrictedColumn);
        Schema::table('books', $dropRestrictedColumn);
        Schema::table('bookshelves', $dropRestrictedColumn);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create restricted columns
        $createRestrictedColumn = fn(Blueprint $table) => $table->boolean('restricted')->index()->default(0);
        Schema::table('pages', $createRestrictedColumn);
        Schema::table('chapters', $createRestrictedColumn);
        Schema::table('books', $createRestrictedColumn);
        Schema::table('bookshelves', $createRestrictedColumn);

        // Set restrictions for entities that have a default entity permission assigned
        // Note: Possible loss of data where default entity permissions have been configured
        $restrictEntities = function (string $table, string $morphClass) {
            $toRestrictIds = DB::table('entity_permissions')
                ->where('role_id', '=', 0)
                ->where('entity_type', '=', $morphClass)
                ->pluck('entity_id');
            DB::table($table)->whereIn('id', $toRestrictIds)->update(['restricted' => true]);
        };
        $restrictEntities('pages', 'page');
        $restrictEntities('chapters', 'chapter');
        $restrictEntities('books', 'book');
        $restrictEntities('bookshelves', 'bookshelf');

        // Delete default entity permissions
        DB::table('entity_permissions')->where('role_id', '=', 0)->delete();
    }
};

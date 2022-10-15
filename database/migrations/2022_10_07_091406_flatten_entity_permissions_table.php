<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FlattenEntityPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove entries for non-existing roles (Caused by previous lack of deletion handling)
        $roleIds = DB::table('roles')->pluck('id');
        DB::table('entity_permissions')->whereNotIn('role_id', $roleIds)->delete();

        // Create new table structure for entity_permissions
        Schema::create('new_entity_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('entity_id');
            $table->string('entity_type', 25);
            $table->unsignedInteger('role_id')->index();
            $table->boolean('view')->default(0);
            $table->boolean('create')->default(0);
            $table->boolean('update')->default(0);
            $table->boolean('delete')->default(0);

            $table->index(['entity_id', 'entity_type']);
        });

        // Migrate existing entity_permission data into new table structure

        $subSelect = function (Builder $query, string $action, string $subAlias) {
            $sub = $query->newQuery()->select('action')->from('entity_permissions', $subAlias)
                ->whereColumn('a.restrictable_id', '=', $subAlias . '.restrictable_id')
                ->whereColumn('a.restrictable_type', '=', $subAlias . '.restrictable_type')
                ->whereColumn('a.role_id', '=', $subAlias . '.role_id')
                ->where($subAlias . '.action', '=', $action);
            return $query->selectRaw("EXISTS({$sub->toSql()})", $sub->getBindings());
        };

        $query = DB::table('entity_permissions', 'a')->select([
            'restrictable_id as entity_id',
            'restrictable_type as entity_type',
            'role_id',
            'view'   => fn(Builder $query) => $subSelect($query, 'view', 'b'),
            'create' => fn(Builder $query) => $subSelect($query, 'create', 'c'),
            'update' => fn(Builder $query) => $subSelect($query, 'update', 'd'),
            'delete' => fn(Builder $query) => $subSelect($query, 'delete', 'e'),
        ])->groupBy('restrictable_id', 'restrictable_type', 'role_id');

        DB::table('new_entity_permissions')->insertUsing(['entity_id', 'entity_type', 'role_id', 'view', 'create', 'update', 'delete'], $query);

        // Drop old entity_permissions table and replace with new structure
        Schema::dropIfExists('entity_permissions');
        Schema::rename('new_entity_permissions', 'entity_permissions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Create old table structure for entity_permissions
        Schema::create('old_entity_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restrictable_id');
            $table->string('restrictable_type', 191);
            $table->integer('role_id')->index();
            $table->string('action', 191)->index();

            $table->index(['restrictable_id', 'restrictable_type']);
        });

        // Convert newer data format to old data format, and insert into old database

        $actionQuery = function (Builder $query, string $action) {
            return $query->select([
                'entity_id as restrictable_id',
                'entity_type as restrictable_type',
                'role_id',
            ])->selectRaw("? as action", [$action])
            ->from('entity_permissions')
            ->where($action, '=', true);
        };

        $query = $actionQuery(DB::query(), 'view')
            ->union(fn(Builder $query) => $actionQuery($query, 'create'))
            ->union(fn(Builder $query) => $actionQuery($query, 'update'))
            ->union(fn(Builder $query) => $actionQuery($query, 'delete'));

        DB::table('old_entity_permissions')->insertUsing(['restrictable_id', 'restrictable_type', 'role_id', 'action'], $query);

        // Drop new entity_permissions table and replace with old structure
        Schema::dropIfExists('entity_permissions');
        Schema::rename('old_entity_permissions', 'entity_permissions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected static array $toNullify = [
        'attachments' => ['created_by', 'updated_by'],
        'comments' => ['created_by', 'updated_by'],
        'deletions' => ['deleted_by'],
        'entities' => ['created_by', 'updated_by', 'owned_by'],
        'images' => ['created_by', 'updated_by'],
        'imports' => ['created_by'],
        'joint_permissions' => ['owner_id'],
        'page_revisions' => ['created_by'],
    ];

    protected static array $toClean = [
        'api_tokens' => ['user_id'],
        'email_confirmations' => ['user_id'],
        'favourites' => ['user_id'],
        'mfa_values' => ['user_id'],
        'role_user' => ['user_id'],
        'sessions' => ['user_id'],
        'social_accounts' => ['user_id'],
        'user_invites' => ['user_id'],
        'views' => ['user_id'],
        'watches' => ['user_id'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $idSelectQuery = DB::table('users')->select('id');

        foreach (self::$toNullify as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $columnName) {
                    $table->unsignedInteger($columnName)->nullable()->change();
                }
            });

            foreach ($columns as $columnName) {
                DB::table($tableName)->where($columnName, '=', 0)->update([$columnName => null]);
                DB::table($tableName)->whereNotIn($columnName, $idSelectQuery)->update([$columnName => null]);
            }
        }

        foreach (self::$toClean as $tableName => $columns) {
            foreach ($columns as $columnName) {
                DB::table($tableName)->whereNotIn($columnName, $idSelectQuery)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::$toNullify as $tableName => $columns) {
            foreach ($columns as $columnName) {
                DB::table($tableName)->whereNull($columnName)->update([$columnName => 0]);
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                foreach ($columns as $columnName) {
                    $table->unsignedInteger($columnName)->nullable(false)->change();
                }
            });
        }
    }
};

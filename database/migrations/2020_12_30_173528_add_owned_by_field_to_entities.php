<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddOwnedByFieldToEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = ['pages', 'books', 'chapters', 'bookshelves'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->integer('owned_by')->unsigned()->index();
            });

            DB::table($table)->update(['owned_by' => DB::raw('`created_by`')]);
        }

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->renameColumn('created_by', 'owned_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = ['pages', 'books', 'chapters', 'bookshelves'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('owned_by');
            });
        }

        Schema::table('joint_permissions', function (Blueprint $table) {
            $table->renameColumn('owned_by', 'created_by');
        });
    }
}

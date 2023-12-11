<?php

use Illuminate\Database\Migrations\Migration;
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
        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('key', 'type');
            $table->renameColumn('extra', 'detail');
            $table->dropColumn('book_id');
            $table->integer('entity_id')->nullable()->change();
            $table->string('entity_type', 191)->nullable()->change();
        });

        DB::table('activities')
            ->where('entity_id', '=', 0)
            ->update([
                'entity_id'   => null,
                'entity_type' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('activities')
            ->whereNull('entity_id')
            ->update([
                'entity_id'   => 0,
                'entity_type' => '',
            ]);

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('type', 'key');
            $table->renameColumn('detail', 'extra');
            $table->integer('book_id');

            $table->integer('entity_id')->change();
            $table->string('entity_type', 191)->change();

            $table->index('book_id');
        });
    }
};

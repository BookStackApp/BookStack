<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommentsAddActiveCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // add column active
            $table->boolean('active')->default(true);
            $table->dropIndex('comments_page_id_parent_id_index');
            $table->index(['page_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            // reversing the schema
            $table->dropIndex('comments_page_id_index');
            $table->dropColumn('active');
            $table->index(['page_id', 'parent_id']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(true);
            $table->integer('book_id')->nullable(true)->unsigned();
            $table->timestamps();
        });
        Schema::create('book_revision_has_page_revision', function (Blueprint $table) {
            $table->integer('page_revision_id')->unsigned();
            $table->bigInteger('book_revision_id')->unsigned();
            $table->primary(['page_revision_id','book_revision_id'], 'page_revision_id_book_revision_id_pk');
        });
        Schema::table('book_revisions', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books');
        });
        Schema::table('book_revision_has_page_revision', function (Blueprint $table) {
            $table->foreign('page_revision_id')->references('id')->on('page_revisions');
            $table->foreign('book_revision_id')->references('id')->on('book_revisions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_revision_has_page_revision', function (Blueprint $table) {
            $table->dropForeign('page_revision_id');
            $table->dropForeign('book_revision_id');
        });
        if (Schema::hasTable('book_revisions')) {
            Schema::table('book_revisions', function (Blueprint $table) {
                $table->dropForeign(['book_id']);
            });
        }
        Schema::dropIfExists('book_revision_has_page_revision');
        Schema::dropIfExists('book_revisions');
    }
}

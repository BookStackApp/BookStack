<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarkdownSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->longText('markdown');
        });

        Schema::table('page_revisions', function (Blueprint $table) {
            $table->longText('markdown');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('markdown');
        });

        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropColumn('markdown');
        });
    }
}

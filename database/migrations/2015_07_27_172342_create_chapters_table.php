<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pdo = \DB::connection()->getPdo();
        $mysqlVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        $requiresISAM = strpos($mysqlVersion, '5.5') === 0;

        Schema::create('chapters', function (Blueprint $table) use ($requiresISAM) {
            if($requiresISAM) $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('book_id');
            $table->string('slug')->indexed();
            $table->text('name');
            $table->text('description');
            $table->integer('priority');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('chapters');
    }
}

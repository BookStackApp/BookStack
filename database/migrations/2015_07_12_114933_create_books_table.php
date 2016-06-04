<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
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

        Schema::create('books', function (Blueprint $table) use ($requiresISAM) {
	        if($requiresISAM) $table->engine = 'MyISAM';
            
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->indexed();
            $table->text('description');
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
        Schema::drop('books');
    }
}

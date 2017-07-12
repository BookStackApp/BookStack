<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDbEncodingToUt8mb4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $database = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $pdo = DB::getPdo();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $pdo->exec('ALTER DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $key = 'Tables_in_' . $database;
        foreach ($tables as $table) {
            $tableName = $table->$key;
            $pdo->exec('ALTER TABLE `'.$tableName.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $database = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $pdo = DB::getPdo();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $pdo->exec('ALTER DATABASE `'.$database.'` CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        $key = 'Tables_in_' . $database;
        foreach ($tables as $table) {
            $tableName = $table->$key;
            $pdo->exec('ALTER TABLE `'.$tableName.'` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci');
        }
    }
}

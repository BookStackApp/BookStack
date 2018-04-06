<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
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

        Schema::create('links', function (Blueprint $table) use ($requiresISAM) {
            if($requiresISAM) $table->engine = 'MyISAM';

            $table->increments('id');
            $table->integer('book_id');
            $table->integer('chapter_id');

            $table->string('name');
            $table->string('slug')->indexed();
            $table->string('link_to');

            $table->integer('priority');
            $table->integer('created_by');
            $table->integer('updated_by');

            $table->boolean('restricted')->default(false);
            $table->index('restricted');

            $table->timestamps();

            // Create default CRUD permissions and allocate to admins and editors
            $entities = ['Link'];
            $ops = ['Create', 'Update', 'Delete', 'View-All', 'View-Own'];
            foreach ($entities as $entity) {
                foreach ($ops as $op) {
                    $newPermId = DB::table('role_permissions')->insertGetId([
                        'name' => strtolower($entity) . '-' . strtolower($op),
                        'display_name' => $op . ' ' . $entity . 's',
                        'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                    ]);
                    DB::table('permission_role')->insert([
                        ['permission_id' => $newPermId, 'role_id' => 1],
                        ['permission_id' => $newPermId, 'role_id' => 2]
                    ]);
                }
            }
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}

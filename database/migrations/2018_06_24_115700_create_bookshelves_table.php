<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookshelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookshelves', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('slug', 200);
            $table->text('description');
            $table->integer('created_by')->nullable()->default(null);
            $table->integer('updated_by')->nullable()->default(null);
            $table->boolean('restricted')->default(false);
            $table->integer('image_id')->nullable()->default(null);
            $table->timestamps();

            $table->index('slug');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('restricted');
        });

        // Get roles with permissions we need to change
        $adminRoleId = DB::table('roles')->where('system_name', '=', 'admin')->first()->id;
        $editorRole = DB::table('roles')->where('name', '=', 'editor')->first();

        // TODO - Copy existing role permissions from Books
        $entity = 'BookShelf';
        $ops = ['View All', 'View Own', 'Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        foreach ($ops as $op) {
            $permId = DB::table('permissions')->insertGetId([
                'name' => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                'display_name' => $op . ' ' . 'BookShelves',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            // Assign view permission to all current roles
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permId
            ]);
            if ($editorRole !== null) {
                DB::table('permission_role')->insert([
                    'role_id' => $editorRole->id,
                    'permission_id' => $permId
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookshelves');
    }
}

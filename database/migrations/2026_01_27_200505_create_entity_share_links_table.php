<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entity_share_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('entity_type', 10)->index();
            $table->string('token', 32)->unique();
            $table->string('name')->nullable();
            $table->unsignedInteger('created_by')->index();
            $table->timestamps();

            $table->index(['entity_id', 'entity_type']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_share_links');
    }
};

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
        Schema::create('mention_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mentionable_type', 50)->index();
            $table->unsignedBigInteger('mentionable_id')->index();
            $table->unsignedInteger('from_user_id');
            $table->unsignedInteger('to_user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mention_history');
    }
};

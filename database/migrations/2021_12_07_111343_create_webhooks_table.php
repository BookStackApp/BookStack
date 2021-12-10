<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('endpoint', 500);
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('webhook_tracked_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('webhook_id');
            $table->string('event', 50);
            $table->timestamps();

            $table->index('event');
            $table->index('webhook_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('webhook_tracked_events');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlueprintWebhookConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blueprint_webhook_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('webhook_url');
            $table->unsignedBigInteger('corporation_id')->nullable(); // null = all corporations
            $table->boolean('notify_created')->default(true);
            $table->boolean('notify_approved')->default(true);
            $table->boolean('notify_rejected')->default(true);
            $table->boolean('notify_fulfilled')->default(true);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blueprint_webhook_configs');
    }
}

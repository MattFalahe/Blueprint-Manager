<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolePingsToWebhookConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blueprint_webhook_configs', function (Blueprint $table) {
            $table->string('ping_role_created', 50)->nullable()->after('notify_created');
            $table->string('ping_role_approved', 50)->nullable()->after('notify_approved');
            $table->string('ping_role_rejected', 50)->nullable()->after('notify_rejected');
            $table->string('ping_role_fulfilled', 50)->nullable()->after('notify_fulfilled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blueprint_webhook_configs', function (Blueprint $table) {
            $table->dropColumn([
                'ping_role_created',
                'ping_role_approved',
                'ping_role_rejected',
                'ping_role_fulfilled'
            ]);
        });
    }
}

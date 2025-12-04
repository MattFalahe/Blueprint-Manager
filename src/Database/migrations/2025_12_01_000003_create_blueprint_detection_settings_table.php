<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlueprintDetectionSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('blueprint_detection_settings', function (Blueprint $table) {
            // CRITICAL: Use bigInteger (NOT unsigned) to match corporation_infos.corporation_id type
            $table->bigInteger('corporation_id');
            $table->json('hangar_divisions')->nullable();
            $table->timestamps();
            
            $table->primary('corporation_id');
            
            $table->foreign('corporation_id')
                ->references('corporation_id')
                ->on('corporation_infos')
                ->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('blueprint_detection_settings');
    }
}

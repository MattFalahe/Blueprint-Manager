<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlueprintLibraryVisibilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('blueprint_library_visibility')) {
            return;
        }

        Schema::create('blueprint_library_visibility', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('corporation_id')->unique();
            // corp | corporations | alliance | all. Default 'corp' = private to
            // the owning corp, which is the historical behaviour for any corp
            // without a row here.
            $table->string('visibility_mode', 20)->default('corp');
            // Allowlist of corp IDs that may view the library when
            // visibility_mode = corporations. Null for every other mode.
            $table->json('shared_corporation_ids')->nullable();
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
        Schema::dropIfExists('blueprint_library_visibility');
    }
}

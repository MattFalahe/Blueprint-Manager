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
        Schema::create('blueprint_container_configs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('corporation_id')->index();
            $table->string('container_name', 255);
            $table->string('display_category', 100); // 'BPO', 'BPC', or custom category name
            $table->enum('match_type', ['exact', 'contains', 'starts_with'])->default('exact');
            $table->boolean('enabled')->default(true);
            $table->integer('priority')->default(0); // For sorting display order
            $table->timestamps();
            
            // Ensure unique combination per corporation
            $table->unique(['corporation_id', 'container_name', 'match_type'], 'corp_container_unique');
            
            $table->index(['corporation_id', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blueprint_container_configs');
    }
};

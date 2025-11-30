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
        Schema::create('blueprint_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('corporation_id')->index();
            $table->bigInteger('character_id'); // Who requested
            $table->integer('blueprint_type_id'); // EVE type_id
            $table->integer('quantity')->default(1); // How many copies
            $table->integer('runs')->nullable(); // Requested runs per copy (for BPCs)
            $table->enum('status', ['pending', 'approved', 'fulfilled', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // Requester notes
            $table->text('response_notes')->nullable(); // Manager response
            $table->bigInteger('approved_by')->nullable(); // character_id of approver
            $table->timestamp('approved_at')->nullable();
            $table->bigInteger('fulfilled_by')->nullable(); // character_id of fulfiller
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
            
            $table->index(['corporation_id', 'status']);
            $table->index(['character_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blueprint_requests');
    }
};

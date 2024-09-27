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
        Schema::create('sms_telemetries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_garden_id')->constrained('sms_gardens')->cascadeOnDelete();
            $table->decimal('latitude', 20, 15);
            $table->decimal('longitude', 20, 15);
            $table->json('samples');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_telemetries');
    }
};

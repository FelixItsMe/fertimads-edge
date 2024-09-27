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
        Schema::create('sms_gardens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portable_device_id')->constrained('portable_devices')->cascadeOnDelete();
            $table->foreignId('garden_id')->constrained('gardens')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_gardens');
    }
};

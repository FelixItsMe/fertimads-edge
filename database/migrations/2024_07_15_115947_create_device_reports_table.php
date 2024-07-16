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
        Schema::create('device_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_selenoid_id')->constrained('device_selenoids')->cascadeOnDelete();
            $table->string('mode');
            $table->string('type');
            $table->string('by_sensor');
            $table->unsignedInteger('total_time');
            $table->float('total_volume');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_reports');
    }
};

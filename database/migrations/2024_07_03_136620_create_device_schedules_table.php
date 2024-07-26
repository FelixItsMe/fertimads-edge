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
        Schema::create('device_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_selenoid_id')->constrained('device_selenoids')->cascadeOnDelete();
            $table->foreignId('garden_id')->constrained('gardens')->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->unsignedInteger('commodity_age')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->time('execute_time');
            $table->unsignedTinyInteger('is_finished')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_schedules');
    }
};

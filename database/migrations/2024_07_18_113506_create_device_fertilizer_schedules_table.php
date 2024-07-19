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
        Schema::create('device_fertilizer_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_selenoid_id')->constrained('device_selenoids')->cascadeOnDelete();
            $table->unsignedTinyInteger('is_finished')->default(0);
            $table->unsignedTinyInteger('type')->comment('1 = n, 2 = p, 3 = k');
            $table->dateTime('execute_start');
            $table->dateTime('execute_end')->nullable();
            $table->float('total_volume');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_fertilizer_schedules');
    }
};

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
        Schema::create('device_fertilize_schedule_executes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_fertilizer_schedule_id')
                ->constrained('device_fertilizer_schedules', 'id', 'device_fertilizer_schedule_id_foreign')
                ->cascadeOnDelete();
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
        Schema::dropIfExists('device_fertilize_schedule_executes');
    }
};

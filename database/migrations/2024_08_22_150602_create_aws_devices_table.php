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
        Schema::create('aws_devices', function (Blueprint $table) {
            $table->id();
            $table->string('series')->unique();
            $table->string('picture');
            $table->decimal('latitude', 20, 15);
            $table->decimal('longitude', 20, 15);
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();
            $table->float('wind_speed')->nullable();
            $table->float('rainfall')->nullable();
            $table->float('max_temp')->nullable();
            $table->float('min_temp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aws_devices');
    }
};

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
        Schema::create('fix_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('is_last_exported')->default(0);
            $table->string('garden_id')->nullable();
            $table->json('samples')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fix_stations');
    }
};
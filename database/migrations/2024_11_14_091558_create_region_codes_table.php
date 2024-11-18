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
        Schema::create('region_codes', function (Blueprint $table) {
            $table->id();
            $table->string('level_1');
            $table->string('level_2')->nullable();
            $table->string('level_3')->nullable();
            $table->string('level_4')->nullable();
            $table->string('full_code');
            $table->string('region_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_codes');
    }
};

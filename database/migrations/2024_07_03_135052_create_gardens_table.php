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
        Schema::create('gardens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('latitude', 20, 15);
            $table->decimal('longitude', 20, 15);
            $table->double('altitude');
            $table->json('polygon');
            $table->double('area');
            $table->string('color', 6);
            $table->unsignedInteger('count_block')->default(0);
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignId('land_id')->constrained('lands')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gardens');
    }
};

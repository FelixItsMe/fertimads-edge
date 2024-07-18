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
        Schema::create('pests', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->string('pest_name');
            $table->string('disease_name');
            $table->foreignId('garden_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained()->cascadeOnDelete();
            $table->integer('infected_count')->default(0);
            $table->text('gemini_prompt');
            $table->text('gemini_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pests');
    }
};

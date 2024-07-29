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
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('name');
            $table->string('category');
            $table->text('symptoms');
            $table->text('cause');
            $table->text('control');
            $table->string('pestisida');
            $table->string('works_category');
            $table->text('chemical');
            $table->text('active_materials');
            $table->text('cure_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
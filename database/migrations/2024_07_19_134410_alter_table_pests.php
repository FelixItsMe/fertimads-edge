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
        Schema::table('pests', function (Blueprint $table) {
            $table->text('disease_name')->change();
            $table->text('pest_name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pests', function (Blueprint $table) {
            $table->string('disease_name')->change();
            $table->string('pest_name')->change();
        });
    }
};

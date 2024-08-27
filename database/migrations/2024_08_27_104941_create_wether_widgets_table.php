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
        Schema::create('wether_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aws_device_id')->nullable()->constrained('aws_devices')->nullOnDelete();
            $table->string('open_api')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wether_widgets');
    }
};

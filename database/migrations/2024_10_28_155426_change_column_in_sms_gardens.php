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
        Schema::table('sms_gardens', function (Blueprint $table) {
            $table->dropForeign(['portable_device_id']);
            $table->dropColumn('portable_device_id');

            $table->after('garden_id', function(Blueprint $table){
                $table->foreignId('device_id')->nullable()->constrained('devices')->cascadeOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_gardens', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};

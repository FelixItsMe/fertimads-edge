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
        Schema::table('wether_widgets', function (Blueprint $table) {
            $table->string('region_code')->nullable()->after('open_api');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wether_widgets', function (Blueprint $table) {
            $table->dropColumn('region_code');
        });
    }
};

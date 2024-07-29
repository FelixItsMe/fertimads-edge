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
        Schema::create('weeds', function (Blueprint $table) {
            $table->id();
            $table->string('foto');
            $table->string('nama_gulma');
            $table->text('deskripsi');
            $table->text('pengendalian');
            $table->string('jenis_pestisida');
            $table->string('klasifikasi_berdasarkan_cara_kerja');
            $table->string('golongan_senyawa_kimia');
            $table->string('bahan_aktif');
            $table->string('nama_obat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weeds');
    }
};

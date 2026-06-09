<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Cuti Tahunan, Sakit, Izin, etc
            $table->string('kode')->unique();
            $table->integer('kuota_per_tahun')->default(0); // 0 = unlimited
            $table->boolean('perlu_persetujuan')->default(true);
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};
 

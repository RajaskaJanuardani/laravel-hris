<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_gaji', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Gaji Pokok, Tunjangan Transport, etc
            $table->string('kode')->unique();
            $table->enum('tipe', ['income', 'deduction']); // Penghasilan atau Potongan
            $table->enum('tipe_perhitungan', ['fixed', 'percentage', 'manual'])->default('fixed');
            $table->decimal('nominal_default', 12, 2)->default(0);
            $table->integer('urutan_tampil')->default(0);
            $table->boolean('aktif')->default(true);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('komponen_gaji');
    }
};

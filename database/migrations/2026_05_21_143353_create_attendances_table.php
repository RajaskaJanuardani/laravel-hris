<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal_absensi');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave', 'sick', 'holiday'])->default('absent');
            $table->unsignedInteger('menit_telat')->default(0);
            $table->decimal('jam_lembur', 5, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['karyawan_id', 'tanggal_absensi']);
            $table->index(['tanggal_absensi', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

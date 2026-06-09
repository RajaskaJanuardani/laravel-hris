<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slip_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->foreignId('periode_penggajian_id')->constrained('periode_penggajian')->onDelete('restrict');
            $table->date('tanggal_penggajian');
            
            // Attendance data
            $table->integer('hari_kerja')->default(0);
            $table->integer('hari_tidak_hadir')->default(0);
            $table->integer('jumlah_telat')->default(0);
            $table->unsignedInteger('total_menit_telat')->default(0);
            $table->decimal('potongan_telat', 12, 2)->default(0);
            $table->decimal('jam_lembur', 8, 2)->default(0);
            $table->decimal('upah_lembur', 12, 2)->default(0);
            
            // Salary breakdown
            $table->decimal('tarif_harian', 12, 2)->default(0);
            $table->decimal('gaji_pokok', 12, 2);
            $table->decimal('total_tunjangan', 12, 2)->default(0);
            $table->decimal('bonus_thr', 12, 2)->default(0);
            $table->decimal('total_potongan', 12, 2)->default(0);
            $table->decimal('gaji_bersih', 12, 2);
            
            $table->enum('status', ['draft', 'final', 'paid'])->default('draft');
            $table->timestamp('dibayar_pada')->nullable();
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['karyawan_id', 'periode_penggajian_id']);
            $table->index(['tanggal_penggajian', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('slip_gaji');
    }
};
 

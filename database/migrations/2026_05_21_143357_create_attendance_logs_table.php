<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->foreignId('kartu_rfid_id')->nullable()->constrained('kartu_rfid')->onDelete('set null');
            $table->string('uid');
            $table->string('sumber')->default('simulator');
            $table->string('nama_perangkat')->nullable();
            $table->ipAddress('alamat_ip')->nullable();
            $table->enum('tipe_scan', ['check_in', 'check_out', 'unknown'])->default('unknown');
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->string('pesan')->nullable();
            $table->timestamp('dipindai_pada')->useCurrent();
            $table->json('data_payload')->nullable();
            $table->timestamps();

            $table->index(['uid', 'dipindai_pada']);
            $table->index(['status', 'tipe_scan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_absensi');
    }
};

<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->unique()->constrained('pengguna')->onDelete('cascade');
            $table->string('nomor_karyawan')->unique();
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('email')->unique();
            $table->string('telepon')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['male', 'female'])->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jabatan', ['staff', 'mandor'])->default('staff');

            $table->date('tanggal_masuk');
            $table->date('tanggal_selesai_kontrak')->nullable();
            $table->decimal('tarif_harian', 12, 2)->default(0);
            $table->enum('tipe_karyawan', ['permanent', 'contract', 'internship'])->default('permanent');
            $table->boolean('aktif')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};

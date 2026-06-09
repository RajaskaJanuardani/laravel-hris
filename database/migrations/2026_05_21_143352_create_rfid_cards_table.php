<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_rfid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->string('uid')->unique(); // RFID UID dari kartu
            $table->string('label_kartu')->nullable();
            $table->enum('status', ['active', 'inactive', 'lost'])->default('active');
            $table->timestamp('diterbitkan_pada')->useCurrent();
            $table->timestamp('kedaluwarsa_pada')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['uid', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('kartu_rfid');
    }
};

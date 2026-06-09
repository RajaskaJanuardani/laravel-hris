<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_penggajian', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // Mei 2026, Juni 2026, etc
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'locked', 'paid', 'archived'])->default('draft');
            $table->timestamp('tanggal_penggajian')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('periode_penggajian');
    }
};

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
    Schema::create('pengguna', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('email')->unique();
        $table->timestamp('email_diverifikasi_pada')->nullable();
        $table->string('kata_sandi');
        $table->enum('peran', ['admin', 'employee'])
                ->default('employee');

        $table->boolean('aktif')->default(true);
        $table->string('token_ingat', 100)->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::dropIfExists('pengguna');
}
};

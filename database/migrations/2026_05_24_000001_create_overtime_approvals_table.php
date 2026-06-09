<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pengguna')) {
            DB::statement("ALTER TABLE pengguna MODIFY peran ENUM('super_admin','hr_admin','manager','admin','employee') NOT NULL DEFAULT 'employee'");
            DB::table('pengguna')
                ->whereIn('peran', ['super_admin', 'hr_admin', 'manager'])
                ->update(['peran' => 'admin']);
            DB::statement("ALTER TABLE pengguna MODIFY peran ENUM('admin','employee') NOT NULL DEFAULT 'employee'");
        }

        if (Schema::hasTable('karyawan') && ! Schema::hasColumn('karyawan', 'jabatan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->enum('jabatan', ['staff', 'mandor'])->default('staff')->after('alamat');
            });
        }

        Schema::create('persetujuan_lembur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai')->default('17:00:00');
            $table->time('jam_selesai')->default('22:00:00');
            $table->enum('status', ['approved', 'cancelled'])->default('approved');
            $table->text('catatan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('pengguna')->onDelete('set null');
            $table->timestamps();

            $table->unique(['karyawan_id', 'tanggal_lembur']);
            $table->index(['tanggal_lembur', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persetujuan_lembur');
    }
};

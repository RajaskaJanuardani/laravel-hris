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

        if (Schema::hasTable('kartu_rfid') && ! Schema::hasColumn('kartu_rfid', 'label_kartu')) {
            Schema::table('kartu_rfid', function (Blueprint $table) {
                $table->string('label_kartu')->nullable()->after('uid');
            });
        }

        if (Schema::hasTable('karyawan') && ! Schema::hasColumn('karyawan', 'jabatan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->enum('jabatan', ['staff', 'mandor'])->default('staff')->after('alamat');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kartu_rfid') && Schema::hasColumn('kartu_rfid', 'label_kartu')) {
            Schema::table('kartu_rfid', function (Blueprint $table) {
                $table->dropColumn('label_kartu');
            });
        }

        if (Schema::hasTable('karyawan') && Schema::hasColumn('karyawan', 'jabatan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn('jabatan');
            });
        }
    }
};

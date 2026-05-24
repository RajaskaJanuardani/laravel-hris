<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','hr_admin','manager','admin','employee') NOT NULL DEFAULT 'employee'");
            DB::table('users')
                ->whereIn('role', ['super_admin', 'hr_admin', 'manager'])
                ->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','employee') NOT NULL DEFAULT 'employee'");
        }

        if (Schema::hasTable('shift_times') && ! Schema::hasColumn('shift_times', 'late_tolerance_minutes')) {
            Schema::table('shift_times', function (Blueprint $table) {
                $table->unsignedInteger('late_tolerance_minutes')->default(10)->after('working_hours');
            });
        }

        if (Schema::hasTable('rfid_cards') && ! Schema::hasColumn('rfid_cards', 'card_label')) {
            Schema::table('rfid_cards', function (Blueprint $table) {
                $table->string('card_label')->nullable()->after('uid');
            });
        }

        if (Schema::hasTable('employees') && ! Schema::hasColumn('employees', 'job_role')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('job_role', ['staff', 'mandor'])->default('staff')->after('shift_time_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rfid_cards') && Schema::hasColumn('rfid_cards', 'card_label')) {
            Schema::table('rfid_cards', function (Blueprint $table) {
                $table->dropColumn('card_label');
            });
        }

        if (Schema::hasTable('shift_times') && Schema::hasColumn('shift_times', 'late_tolerance_minutes')) {
            Schema::table('shift_times', function (Blueprint $table) {
                $table->dropColumn('late_tolerance_minutes');
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'job_role')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('job_role');
            });
        }
    }
};

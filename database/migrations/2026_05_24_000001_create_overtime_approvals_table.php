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

        if (Schema::hasTable('employees') && ! Schema::hasColumn('employees', 'job_role')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('job_role', ['staff', 'mandor'])->default('staff')->after('shift_time_id');
            });
        }

        Schema::create('overtime_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('overtime_date');
            $table->time('start_time')->default('17:00:00');
            $table->time('end_time')->default('22:00:00');
            $table->enum('status', ['approved', 'cancelled'])->default('approved');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['employee_id', 'overtime_date']);
            $table->index(['overtime_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_approvals');
    }
};

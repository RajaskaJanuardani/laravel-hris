<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('restrict');
            $table->date('payroll_date');
            
            // Attendance data
            $table->integer('working_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_count')->default(0);
            $table->decimal('late_deduction', 12, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
            
            // Salary breakdown
            $table->decimal('base_salary', 12, 2);
            $table->decimal('total_allowance', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            
            $table->enum('status', ['draft', 'final', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['employee_id', 'payroll_period_id']);
            $table->index(['payroll_date', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
 
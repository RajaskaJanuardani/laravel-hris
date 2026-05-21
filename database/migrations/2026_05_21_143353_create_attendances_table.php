<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave', 'sick', 'holiday'])->default('absent');
            $table->unsignedInteger('late_minutes')->default(0);
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['employee_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
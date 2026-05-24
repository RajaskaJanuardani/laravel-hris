<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('position_id')->constrained()->onDelete('restrict');
            $table->foreignId('shift_time_id')->constrained()->onDelete('restrict');
            $table->enum('job_role', ['staff', 'mandor'])->default('staff');
            
            $table->date('hire_date');
            $table->date('contract_end_date')->nullable();
            $table->decimal('salary', 12, 2)->default(0);
            $table->enum('employment_type', ['permanent', 'contract', 'internship'])->default('permanent');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'department_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

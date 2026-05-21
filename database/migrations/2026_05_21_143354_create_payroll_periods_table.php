<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Mei 2026, Juni 2026, etc
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'locked', 'paid', 'archived'])->default('draft');
            $table->timestamp('payroll_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['start_date', 'end_date']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Gaji Pokok, Tunjangan Transport, etc
            $table->string('code')->unique();
            $table->enum('type', ['income', 'deduction']); // Penghasilan atau Potongan
            $table->enum('calculation_type', ['fixed', 'percentage', 'manual'])->default('fixed');
            $table->decimal('default_amount', 12, 2)->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
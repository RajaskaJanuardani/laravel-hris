<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Cuti Tahunan, Sakit, Izin, etc
            $table->string('code')->unique();
            $table->integer('quota_per_year')->default(0); // 0 = unlimited
            $table->boolean('requires_approval')->default(true);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
 
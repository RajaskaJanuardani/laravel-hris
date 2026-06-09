<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_times', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('working_hours')->default(8);
            $table->unsignedInteger('late_tolerance_minutes')->default(10);
            $table->text('description')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('shift_times');
    }
};

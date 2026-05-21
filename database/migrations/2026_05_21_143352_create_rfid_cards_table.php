<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfid_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('uid')->unique(); // RFID UID dari kartu
            $table->enum('status', ['active', 'inactive', 'lost'])->default('active');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expired_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['uid', 'status']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
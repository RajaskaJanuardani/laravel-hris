<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('rfid_card_id')->nullable()->constrained()->onDelete('set null');
            $table->string('uid');
            $table->string('source')->default('simulator');
            $table->string('device_name')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->enum('scan_type', ['check_in', 'check_out', 'unknown'])->default('unknown');
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->string('message')->nullable();
            $table->timestamp('scanned_at')->useCurrent();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['uid', 'scanned_at']);
            $table->index(['status', 'scan_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

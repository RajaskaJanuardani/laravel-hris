<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'rfid_card_id',
        'uid',
        'source',
        'device_name',
        'ip_address',
        'scan_type',
        'status',
        'message',
        'scanned_at',
        'payload',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'payload' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function rfidCard(): BelongsTo
    {
        return $this->belongsTo(RFIDCard::class);
    }
}

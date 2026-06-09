<?php

namespace App\Models;

use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasColumnAliases, HasFactory;

    protected $table = 'log_absensi';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'rfid_card_id' => 'kartu_rfid_id',
        'source' => 'sumber',
        'device_name' => 'nama_perangkat',
        'ip_address' => 'alamat_ip',
        'scan_type' => 'tipe_scan',
        'message' => 'pesan',
        'scanned_at' => 'dipindai_pada',
        'payload' => 'data_payload',
    ];

    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'kartu_rfid_id',
        'rfid_card_id',
        'uid',
        'source',
        'sumber',
        'nama_perangkat',
        'device_name',
        'alamat_ip',
        'ip_address',
        'tipe_scan',
        'scan_type',
        'status',
        'message',
        'pesan',
        'dipindai_pada',
        'scanned_at',
        'payload',
        'data_payload',
    ];

    protected $casts = [
        'dipindai_pada' => 'datetime',
        'data_payload' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }

    public function rfidCard(): BelongsTo
    {
        return $this->belongsTo(RFIDCard::class, 'kartu_rfid_id');
    }
}

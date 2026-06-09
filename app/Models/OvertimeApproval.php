<?php

namespace App\Models;

use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeApproval extends Model
{
    use HasColumnAliases, HasFactory;

    protected $table = 'persetujuan_lembur';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'overtime_date' => 'tanggal_lembur',
        'start_time' => 'jam_mulai',
        'end_time' => 'jam_selesai',
        'notes' => 'catatan',
        'approved_by' => 'disetujui_oleh',
    ];

    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'tanggal_lembur',
        'overtime_date',
        'jam_mulai',
        'start_time',
        'jam_selesai',
        'end_time',
        'status',
        'catatan',
        'notes',
        'disetujui_oleh',
        'approved_by',
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Leave extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'cuti';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'leave_type_id' => 'jenis_cuti_id',
        'start_date' => 'tanggal_mulai',
        'end_date' => 'tanggal_selesai',
        'number_of_days' => 'jumlah_hari',
        'reason' => 'alasan',
        'approved_by' => 'disetujui_oleh',
        'approved_at' => 'disetujui_pada',
        'approval_notes' => 'catatan_persetujuan',
    ];
 
    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'jenis_cuti_id',
        'leave_type_id',
        'tanggal_mulai',
        'start_date',
        'tanggal_selesai',
        'end_date',
        'jumlah_hari',
        'number_of_days',
        'alasan',
        'reason',
        'status',
        'disetujui_oleh',
        'approved_by',
        'disetujui_pada',
        'approved_at',
        'catatan_persetujuan',
        'approval_notes',
    ];
 
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'disetujui_pada' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }
 
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'jenis_cuti_id');
    }
 
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
 
    public function approve(User $user, $catatan = null)
    {
        $this->update([
            'status' => 'approved',
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
            'catatan_persetujuan' => $catatan,
        ]);
    }
 
    public function reject(User $user, $catatan)
    {
        $this->update([
            'status' => 'rejected',
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
            'catatan_persetujuan' => $catatan,
        ]);
    }
 
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
 
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

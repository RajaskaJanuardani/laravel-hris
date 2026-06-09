<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Payslip extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'slip_gaji';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'payroll_period_id' => 'periode_penggajian_id',
        'payroll_date' => 'tanggal_penggajian',
        'working_days' => 'hari_kerja',
        'absent_days' => 'hari_tidak_hadir',
        'late_count' => 'jumlah_telat',
        'late_deduction' => 'potongan_telat',
        'overtime_hours' => 'jam_lembur',
        'overtime_amount' => 'upah_lembur',
        'base_salary' => 'gaji_pokok',
        'total_allowance' => 'total_tunjangan',
        'total_deduction' => 'total_potongan',
        'net_salary' => 'gaji_bersih',
        'paid_at' => 'dibayar_pada',
        'notes' => 'catatan',
    ];
 
    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'periode_penggajian_id',
        'payroll_period_id',
        'tanggal_penggajian',
        'payroll_date',
        'hari_kerja',
        'working_days',
        'hari_tidak_hadir',
        'absent_days',
        'jumlah_telat',
        'late_count',
        'total_menit_telat',
        'potongan_telat',
        'late_deduction',
        'jam_lembur',
        'overtime_hours',
        'upah_lembur',
        'overtime_amount',
        'tarif_harian',
        'gaji_pokok',
        'base_salary',
        'total_tunjangan',
        'total_allowance',
        'bonus_thr',
        'total_potongan',
        'total_deduction',
        'gaji_bersih',
        'net_salary',
        'status',
        'dibayar_pada',
        'paid_at',
        'catatan',
        'notes',
    ];
 
    protected $casts = [
        'tanggal_penggajian' => 'date',
        'total_menit_telat' => 'integer',
        'potongan_telat' => 'decimal:2',
        'jam_lembur' => 'decimal:2',
        'upah_lembur' => 'decimal:2',
        'tarif_harian' => 'decimal:2',
        'gaji_pokok' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'bonus_thr' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'dibayar_pada' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }
 
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'periode_penggajian_id');
    }
 
    public function markAsFinal()
    {
        $this->update(['status' => 'final']);
    }
 
    public function markAsPaid()
    {
        $this->update(['status' => 'paid', 'dibayar_pada' => now()]);
    }
 
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
 
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }
 
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}

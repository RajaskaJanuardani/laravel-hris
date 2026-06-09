<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Attendance extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'absensi';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'attendance_date' => 'tanggal_absensi',
        'check_in_time' => 'jam_masuk',
        'check_out_time' => 'jam_pulang',
        'late_minutes' => 'menit_telat',
        'overtime_hours' => 'jam_lembur',
        'notes' => 'catatan',
    ];
 
    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'tanggal_absensi',
        'attendance_date',
        'jam_masuk',
        'check_in_time',
        'jam_pulang',
        'check_out_time',
        'status',
        'menit_telat',
        'late_minutes',
        'jam_lembur',
        'overtime_hours',
        'catatan',
        'notes',
    ];
 
    protected $casts = [
        'tanggal_absensi' => 'date',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_pulang' => 'datetime:H:i:s',
        'jam_lembur' => 'decimal:2',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }
 
    public function isPresent()
    {
        return in_array($this->status, ['present', 'late']);
    }
 
    public function isLate()
    {
        return $this->status === 'late' && $this->menit_telat > 0;
    }
 
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_absensi', $date);
    }
 
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal_absensi', $month)
            ->whereYear('tanggal_absensi', $year);
    }
}

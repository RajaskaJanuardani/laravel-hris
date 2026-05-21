<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Attendance extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'status',
        'late_minutes',
        'overtime_hours',
        'notes',
    ];
 
    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s',
        'overtime_hours' => 'decimal:2',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
 
    public function isPresent()
    {
        return in_array($this->status, ['present', 'late']);
    }
 
    public function isLate()
    {
        return $this->status === 'late' && $this->late_minutes > 0;
    }
 
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }
 
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);
    }
}
<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Employee extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'department_id',
        'position_id',
        'shift_time_id',
        'hire_date',
        'contract_end_date',
        'salary',
        'employment_type',
        'is_active',
    ];
 
    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
 
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
 
    public function shiftTime(): BelongsTo
    {
        return $this->belongsTo(ShiftTime::class);
    }
 
    public function rfidCards(): HasMany
    {
        return $this->hasMany(RFIDCard::class);
    }
 
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
 
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }
 
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }
 
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
 
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
 
    public function getTodayAttendance()
    {
        return $this->attendances()
            ->whereDate('attendance_date', today())
            ->first();
    }
 
    public function getThisMonthAttendance()
    {
        return $this->attendances()
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();
    }
 
    public function getActiveRFIDCard()
    {
        return $this->rfidCards()
            ->where('status', 'active')
            ->whereNull('expired_at')
            ->orWhere('expired_at', '>', now())
            ->first();
    }
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
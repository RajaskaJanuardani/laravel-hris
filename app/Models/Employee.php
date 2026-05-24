<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
 
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
        'profile_photo_path',
        'department_id',
        'position_id',
        'shift_time_id',
        'job_role',
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

    public function currentLeave(): HasOne
    {
        return $this->hasOne(Leave::class)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->latestOfMany();
    }
 
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }
 
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function overtimeApprovals(): HasMany
    {
        return $this->hasMany(OvertimeApproval::class);
    }
 
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if (! $this->profile_photo_path) {
            return asset('tailadmin-nextjs-1.0.0/public/images/user/owner.jpg');
        }

        return Storage::disk('public')->url($this->profile_photo_path);
    }

    public function getCurrentStatusAttribute(): array
    {
        if (! $this->is_active) {
            return [
                'label' => 'Nonaktif',
                'badge' => 'secondary',
            ];
        }

        $currentLeave = $this->relationLoaded('currentLeave')
            ? $this->getRelation('currentLeave')
            : $this->currentLeave()->first();

        if ($currentLeave) {
            return [
                'label' => 'Cuti',
                'badge' => 'warning',
            ];
        }

        return [
            'label' => 'Aktif',
            'badge' => 'success',
        ];
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

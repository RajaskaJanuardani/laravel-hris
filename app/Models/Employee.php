<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
 
class Employee extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    public const DAILY_RATES = [
        'staff' => 110000,
        'mandor' => 250000,
    ];

    public const WORK_HOURS_PER_DAY = 8;
    public const OVERTIME_MULTIPLIER = 1.5;
    public const THR_WORKING_DAYS = 26;

    protected $table = 'karyawan';

    protected array $columnAliases = [
        'user_id' => 'pengguna_id',
        'employee_id' => 'nomor_karyawan',
        'first_name' => 'nama_depan',
        'last_name' => 'nama_belakang',
        'phone' => 'telepon',
        'date_of_birth' => 'tanggal_lahir',
        'gender' => 'jenis_kelamin',
        'address' => 'alamat',
        'profile_photo_path' => 'path_foto_profil',
        'job_role' => 'jabatan',
        'hire_date' => 'tanggal_masuk',
        'contract_end_date' => 'tanggal_selesai_kontrak',
        'salary' => 'tarif_harian',
        'employment_type' => 'tipe_karyawan',
        'is_active' => 'aktif',
    ];
 
    protected $fillable = [
        'pengguna_id',
        'user_id',
        'karyawan_id',
        'employee_id',
        'nomor_karyawan',
        'nama_depan',
        'first_name',
        'nama_belakang',
        'last_name',
        'email',
        'telepon',
        'phone',
        'tanggal_lahir',
        'date_of_birth',
        'jenis_kelamin',
        'gender',
        'alamat',
        'address',
        'path_foto_profil',
        'profile_photo_path',
        'jabatan',
        'job_role',
        'tanggal_masuk',
        'hire_date',
        'tanggal_selesai_kontrak',
        'contract_end_date',
        'tarif_harian',
        'salary',
        'tipe_karyawan',
        'employment_type',
        'aktif',
        'is_active',
    ];
 
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_selesai_kontrak' => 'date',
        'tarif_harian' => 'decimal:2',
        'aktif' => 'boolean',
    ];
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function rfidCards(): HasMany
    {
        return $this->hasMany(RFIDCard::class, 'karyawan_id')->latest('id');
    }
 
    public function absensi(): HasMany
    {
        return $this->hasMany(Attendance::class, 'karyawan_id');
    }

    public function attendances(): HasMany
    {
        return $this->absensi();
    }
 
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'karyawan_id');
    }

    public function currentLeave(): HasOne
    {
        return $this->hasOne(Leave::class, 'karyawan_id')
            ->where('status', 'approved')
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_selesai', '>=', today())
            ->latestOfMany();
    }
 
    public function gaji(): HasMany
    {
        return $this->hasMany(Salary::class, 'karyawan_id');
    }

    public function salaries(): HasMany
    {
        return $this->gaji();
    }
 
    public function slip_gaji(): HasMany
    {
        return $this->hasMany(Payslip::class, 'karyawan_id');
    }

    public function payslips(): HasMany
    {
        return $this->slip_gaji();
    }

    public function overtimeApprovals(): HasMany
    {
        return $this->hasMany(OvertimeApproval::class, 'karyawan_id');
    }
 
    public function getFullNameAttribute()
    {
        return "{$this->nama_depan} {$this->nama_belakang}";
    }

    public function getKaryawanIdAttribute(): ?string
    {
        return $this->attributes['nomor_karyawan'] ?? null;
    }

    public function setKaryawanIdAttribute(?string $value): void
    {
        $this->attributes['nomor_karyawan'] = $value;
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if (! $this->path_foto_profil) {
            return asset('tailadmin-nextjs-1.0.0/public/images/user/owner.jpg');
        }

        return Storage::disk('public')->url($this->path_foto_profil);
    }

    public function getCurrentStatusAttribute(): array
    {
        if (! $this->aktif) {
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
        return $this->absensi()
            ->whereDate('tanggal_absensi', today())
            ->first();
    }
 
    public function getThisMonthAttendance()
    {
        return $this->absensi()
            ->whereMonth('tanggal_absensi', now()->month)
            ->whereYear('tanggal_absensi', now()->year)
            ->get();
    }
 
    public function getActiveRFIDCard()
    {
        if ($this->relationLoaded('rfidCards')) {
            return $this->rfidCards->first(fn (RFIDCard $card) => $card->isActive())
                ?? $this->rfidCards->first();
        }

        return $this->rfidCards()
            ->active()
            ->latest('id')
            ->first()
            ?? $this->rfidCards()->latest('id')->first();
    }
 
    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }

    public static function dailyRateFor(?string $jabatan): int
    {
        return self::DAILY_RATES[$jabatan ?: 'staff'] ?? self::DAILY_RATES['staff'];
    }

    public function dailyRate(): int
    {
        return self::dailyRateFor($this->jabatan);
    }

    public function hourlyRate(): float
    {
        return $this->dailyRate() / self::WORK_HOURS_PER_DAY;
    }

    public function thrBonus(): int
    {
        return $this->dailyRate() * self::THR_WORKING_DAYS;
    }
}

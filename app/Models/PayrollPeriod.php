<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PayrollPeriod extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'periode_penggajian';

    protected array $columnAliases = [
        'name' => 'nama',
        'start_date' => 'tanggal_mulai',
        'end_date' => 'tanggal_selesai',
        'payroll_date' => 'tanggal_penggajian',
        'notes' => 'catatan',
    ];
 
    protected $fillable = [
        'name',
        'nama',
        'tanggal_mulai',
        'start_date',
        'tanggal_selesai',
        'end_date',
        'status',
        'tanggal_penggajian',
        'payroll_date',
        'catatan',
        'notes',
    ];
 
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_penggajian' => 'datetime',
    ];
 
    public function slip_gaji(): HasMany
    {
        return $this->hasMany(Payslip::class, 'periode_penggajian_id');
    }

    public function payslips(): HasMany
    {
        return $this->slip_gaji();
    }
 
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
 
    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }
}

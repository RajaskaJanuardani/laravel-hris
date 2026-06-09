<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Salary extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'gaji';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'base_salary' => 'gaji_pokok',
        'total_income' => 'total_pendapatan',
        'total_deduction' => 'total_potongan',
        'net_salary' => 'gaji_bersih',
        'effective_date' => 'tanggal_berlaku',
        'end_date' => 'tanggal_selesai',
        'is_active' => 'aktif',
    ];
 
    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'gaji_pokok',
        'base_salary',
        'total_pendapatan',
        'total_income',
        'total_potongan',
        'total_deduction',
        'gaji_bersih',
        'net_salary',
        'tanggal_berlaku',
        'effective_date',
        'tanggal_selesai',
        'end_date',
        'aktif',
        'is_active',
    ];
 
    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'tanggal_berlaku' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }
 
    public function scopeActive($query)
    {
        return $query->where('aktif', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', now());
            });
    }
}

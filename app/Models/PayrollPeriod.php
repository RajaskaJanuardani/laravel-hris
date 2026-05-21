<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class PayrollPeriod extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'payroll_date',
        'notes',
    ];
 
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payroll_date' => 'datetime',
    ];
 
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
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
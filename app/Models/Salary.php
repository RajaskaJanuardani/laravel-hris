<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Salary extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'employee_id',
        'base_salary',
        'total_income',
        'total_deduction',
        'net_salary',
        'effective_date',
        'end_date',
        'is_active',
    ];
 
    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }
}
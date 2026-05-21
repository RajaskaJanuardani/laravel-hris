<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Payslip extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'employee_id',
        'payroll_period_id',
        'payroll_date',
        'working_days',
        'absent_days',
        'late_count',
        'late_deduction',
        'overtime_hours',
        'overtime_amount',
        'base_salary',
        'total_allowance',
        'total_deduction',
        'net_salary',
        'status',
        'paid_at',
        'notes',
    ];
 
    protected $casts = [
        'payroll_date' => 'date',
        'late_deduction' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
 
    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }
 
    public function markAsFinal()
    {
        $this->update(['status' => 'final']);
    }
 
    public function markAsPaid()
    {
        $this->update(['status' => 'paid', 'paid_at' => now()]);
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
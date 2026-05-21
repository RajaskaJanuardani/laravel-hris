<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class SalaryComponent extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'code',
        'type',
        'calculation_type',
        'default_amount',
        'display_order',
        'is_active',
        'description',
    ];
 
    protected $casts = [
        'default_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
 
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }
 
    public function scopeDeduction($query)
    {
        return $query->where('type', 'deduction');
    }
}
 
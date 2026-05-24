<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class ShiftTime extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'working_hours',
        'late_tolerance_minutes',
        'description',
        'is_active',
    ];
 
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];
 
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

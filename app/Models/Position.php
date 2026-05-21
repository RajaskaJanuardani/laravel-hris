<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Position extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'code',
        'description',
        'salary_min',
        'salary_max',
        'is_active',
    ];
 
    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
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
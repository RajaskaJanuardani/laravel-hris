<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class LeaveType extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'code',
        'quota_per_year',
        'requires_approval',
        'description',
        'is_active',
    ];
 
    protected $casts = [
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];
 
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }
 
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
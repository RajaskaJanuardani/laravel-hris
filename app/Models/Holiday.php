<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Holiday extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'name',
        'date',
        'description',
        'is_annual',
    ];
 
    protected $casts = [
        'date' => 'date',
        'is_annual' => 'boolean',
    ];
 
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
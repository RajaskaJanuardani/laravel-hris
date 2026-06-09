<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Holiday extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'hari_libur';

    protected array $columnAliases = [
        'name' => 'nama',
        'date' => 'tanggal',
        'description' => 'deskripsi',
        'is_annual' => 'tahunan',
    ];
 
    protected $fillable = [
        'name',
        'nama',
        'date',
        'tanggal',
        'description',
        'deskripsi',
        'tahunan',
        'is_annual',
    ];
 
    protected $casts = [
        'tanggal' => 'date',
        'tahunan' => 'boolean',
    ];
 
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }
}

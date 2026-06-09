<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class SalaryComponent extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'komponen_gaji';

    protected array $columnAliases = [
        'name' => 'nama',
        'code' => 'kode',
        'type' => 'tipe',
        'calculation_type' => 'tipe_perhitungan',
        'default_amount' => 'nominal_default',
        'display_order' => 'urutan_tampil',
        'is_active' => 'aktif',
        'description' => 'deskripsi',
    ];
 
    protected $fillable = [
        'name',
        'nama',
        'code',
        'kode',
        'type',
        'tipe',
        'tipe_perhitungan',
        'calculation_type',
        'nominal_default',
        'default_amount',
        'urutan_tampil',
        'display_order',
        'aktif',
        'is_active',
        'description',
        'deskripsi',
    ];
 
    protected $casts = [
        'nominal_default' => 'decimal:2',
        'aktif' => 'boolean',
    ];
 
    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }
 
    public function scopeIncome($query)
    {
        return $query->where('tipe', 'income');
    }
 
    public function scopeDeduction($query)
    {
        return $query->where('tipe', 'deduction');
    }
}
 

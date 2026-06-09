<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class LeaveType extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'jenis_cuti';

    protected array $columnAliases = [
        'name' => 'nama',
        'code' => 'kode',
        'quota_per_year' => 'kuota_per_tahun',
        'requires_approval' => 'perlu_persetujuan',
        'description' => 'deskripsi',
        'is_active' => 'aktif',
    ];
 
    protected $fillable = [
        'name',
        'nama',
        'code',
        'kode',
        'kuota_per_tahun',
        'quota_per_year',
        'perlu_persetujuan',
        'requires_approval',
        'description',
        'deskripsi',
        'aktif',
        'is_active',
    ];
 
    protected $casts = [
        'perlu_persetujuan' => 'boolean',
        'aktif' => 'boolean',
    ];
 
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'jenis_cuti_id');
    }
 
    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }
}

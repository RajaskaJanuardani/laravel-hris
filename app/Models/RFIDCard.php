<?php
 
namespace App\Models;
 
use App\Models\Concerns\HasColumnAliases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class RFIDCard extends Model
{
    use HasColumnAliases, HasFactory, SoftDeletes;

    protected $table = 'kartu_rfid';

    protected array $columnAliases = [
        'employee_id' => 'karyawan_id',
        'card_label' => 'label_kartu',
        'issued_at' => 'diterbitkan_pada',
        'expired_at' => 'kedaluwarsa_pada',
        'notes' => 'catatan',
    ];
 
    protected $fillable = [
        'karyawan_id',
        'employee_id',
        'uid',
        'label_kartu',
        'card_label',
        'status',
        'diterbitkan_pada',
        'issued_at',
        'kedaluwarsa_pada',
        'expired_at',
        'catatan',
        'notes',
    ];
 
    protected $casts = [
        'diterbitkan_pada' => 'datetime',
        'kedaluwarsa_pada' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }
 
    public function isActive()
    {
        return $this->status === 'active' && ($this->kedaluwarsa_pada === null || $this->kedaluwarsa_pada > now());
    }
 
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('kedaluwarsa_pada')
                    ->orWhere('kedaluwarsa_pada', '>', now());
            });
    }
 
    public function scopeByUID($query, $uid)
    {
        return $query->where('uid', $uid);
    }
}

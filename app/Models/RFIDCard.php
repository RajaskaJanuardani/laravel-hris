<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class RFIDCard extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'employee_id',
        'uid',
        'status',
        'issued_at',
        'expired_at',
        'notes',
    ];
 
    protected $casts = [
        'issued_at' => 'datetime',
        'expired_at' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
 
    public function isActive()
    {
        return $this->status === 'active' && ($this->expired_at === null || $this->expired_at > now());
    }
 
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            });
    }
 
    public function scopeByUID($query, $uid)
    {
        return $query->where('uid', $uid);
    }
}

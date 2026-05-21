<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class Leave extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'number_of_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];
 
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];
 
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
 
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
 
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
 
    public function approve(User $user, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }
 
    public function reject(User $user, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }
 
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
 
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
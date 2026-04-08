<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'message', 'status', 'assigned_to',
        'marketer_id', 'value', 'follow_up_date'
    ];

    protected $casts = [
        'follow_up_date' => 'date'
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketer_id');
    }
}

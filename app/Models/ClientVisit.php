<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'realtor_id', 'client_name', 'client_phone', 'client_email',
        'property_id', 'notes', 'outcome', 'offered_price', 'offer_status',
        'manager_id', 'approved', 'visited_at'
    ];

    protected $casts = [
        'visited_at' => 'datetime'
    ];

    public function realtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realtor_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

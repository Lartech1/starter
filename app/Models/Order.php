<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'client_id', 'type', 'description', 'quantity',
        'unit_price', 'total_price', 'status', 'manager_id', 'delivery_address',
        'requested_date', 'delivery_date'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'delivery_date' => 'date'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

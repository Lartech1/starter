<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'realtor_id', 'status'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function realtor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realtor_id');
    }
}

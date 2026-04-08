<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'estate_manager_id', 'title', 'description', 'type', 'status', 'price',
        'rental_price', 'area_size', 'location', 'latitude', 'longitude',
        'bedrooms', 'bathrooms', 'has_bq', 'features', 'image_url', 'images',
        'project_name'
    ];

    protected $casts = [
        'images' => 'json',
        'has_bq' => 'boolean'
    ];

    public function estateManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'estate_manager_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PropertyAssignment::class);
    }

    public function clientVisits(): HasMany
    {
        return $this->hasMany(ClientVisit::class);
    }
}

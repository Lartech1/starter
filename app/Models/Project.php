<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'type', 'location', 'status', 'completion_percentage',
        'start_date', 'end_date', 'budget', 'spent', 'manager_id', 'image_url', 'images'
    ];

    protected $casts = [
        'images' => 'json'
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'field_agent_id', 'completion_percentage',
        'description', 'images', 'expenses', 'notes'
    ];

    protected $casts = [
        'images' => 'json'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function fieldAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'field_agent_id');
    }
}

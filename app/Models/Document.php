<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'file_path', 'file_url', 'uploaded_by',
        'legal_officer_id', 'verified', 'verified_at', 'notes'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function legalOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'legal_officer_id');
    }
}

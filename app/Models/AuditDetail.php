<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuditDetail extends Model
{
    protected $fillable = [
        'audit_id',
        'operator_id',
        'girl_id',
        'platform_id',
        'client_name',
        'client_id',
        'client_status',
        'checklist',
        'general_score',
        'general_observation',
        'recommendations',
        'screenshots'
    ];

    protected $casts = [
        'checklist' => 'array',
        'screenshots' => 'array',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function girl(): BelongsTo
    {
        return $this->belongsTo(Girl::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}

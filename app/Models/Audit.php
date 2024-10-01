<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{

    protected $fillable = [
        'auditor_id',
        'operator_id',
        'conversation_date',
        'review_date',
        'platform_id',
        'group_id',
        'girl_id',
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
        'conversation_date' => 'datetime',
        'review_date' => 'datetime',
        'checklist' => 'array',
        'screenshots' => 'array',
    ];

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
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

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}

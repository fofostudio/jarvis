<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    protected $fillable = [
        'auditor_id',
        'audit_type',  // 'group' or 'individual'
        'group_id',
        'operator_id',
        'audit_date',
        'total_score',
    ];

    protected $casts = [
        'audit_date' => 'datetime',
    ];

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function auditDetails(): HasMany
    {
        return $this->hasMany(AuditDetail::class);
    }
}

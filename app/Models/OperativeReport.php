<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperativeReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_date',
        'report_type',
        'content',
        'content_quejas',
        'conversation_data',
        'file_path',
        'is_approved',
        'group_id',
        'auditor_comment',
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_approved' => 'boolean',
        'conversation_data' => 'array',
    ];


    /**
     * Get the user that owns the operative report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the auditor that reviewed the report.
     */
    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    /**
     * Scope a query to only include approved reports.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include pending reports.
     */
    public function scopePending($query)
    {
        return $query->whereNull('is_approved');
    }

    /**
     * Scope a query to only include rejected reports.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_approved', false);
    }
    protected $appends = ['conversations'];

    public function getConversationsAttribute()
    {
        if ($this->report_type === 'conversational') {
            return $this->conversation_data ?: json_decode($this->content, true);
        }
        return null;
    }
    /**
     * Get the report status.
     */
    public function getStatusAttribute()
    {
        if ($this->is_approved === null) {
            return 'Pendiente';
        }
        return $this->is_approved ? 'Aprobado' : 'Rechazado';
    }

    /**
     * Check if the report is a file.
     */
    public function getIsFileAttribute()
    {
        return $this->report_type === 'file';
    }

    /**
     * Check if the report is manual.
     */
    public function getIsManualAttribute()
    {
        return $this->report_type === 'manual';
    }

    /**
     * Set the is_approved attribute.
     */
    public function setIsApprovedAttribute($value)
    {
        $this->attributes['is_approved'] = $value;
        if ($value !== null) {
            $this->attributes['auditor_id'] = auth()->id();
        }
    }
}

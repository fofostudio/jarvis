<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'week_number',
        'year',
        'type',
    ];

    protected $casts = [
        'week_number' => 'integer',
        'year' => 'integer',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function assignments()
    {
        return $this->hasMany(WorkPlanAssignment::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForWeek($query, $weekNumber, $year)
    {
        return $query->where('week_number', $weekNumber)->where('year', $year);
    }
}

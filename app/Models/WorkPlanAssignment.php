<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlanAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_plan_id',
        'girl_id',
        'day_of_week',
        'shift',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function workPlan()
    {
        return $this->belongsTo(WorkPlan::class);
    }

    public function girl()
    {
        return $this->belongsTo(Girl::class);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeForShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }
}

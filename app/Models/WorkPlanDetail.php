<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'calendar_type',
        'cantidad',
        'mensaje',
        'screenshot_paths',
    ];

    protected $casts = [
        'date' => 'date',
        'screenshot_paths' => 'array',
    ];

    /**
     * Get the user that owns the work plan detail.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full path of the screenshot.
     *
     * @return string
     */
    public function getScreenshotUrlAttribute()
    {
        return asset('storage/' . $this->screenshot_path);
    }

    /**
     * Scope a query to only include work plan details for a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include work plan details for the current week.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentWeek($query)
    {
        return $query->whereBetween('date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }
}

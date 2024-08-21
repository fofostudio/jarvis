<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'expected_end_time',
        'actual_end_time',
        'overtime',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'expected_end_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'overtime' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOngoing()
    {
        return $this->actual_end_time === null;
    }

    public function hasOvertime()
    {
        return $this->overtime > 0;
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->isOngoing()) {
            return 0;
        }

        $now = now();
        $remainingTime = $now->diffInSeconds($this->expected_end_time, false);

        return max(0, $remainingTime);
    }
}

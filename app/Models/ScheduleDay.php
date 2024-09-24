<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduleDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'is_inverted',
        'is_optional',
        'mandatory_shift',
    ];

    protected $casts = [
        'date' => 'date',
        'is_inverted' => 'boolean',
        'is_optional' => 'boolean',

    ];

    public static function getOrCreate($date)
    {
        return self::firstOrCreate(
            ['date' => $date],
            ['is_inverted' => false, 'is_optional' => false, 'mandatory_shift' => null]
        );
    }

    public static function isInverted($date)
    {
        $scheduleDay = self::where('date', $date)->first();
        return $scheduleDay ? $scheduleDay->is_inverted : false;
    }
    public static function isOptional($date)
    {
        $scheduleDay = self::where('date', $date)->first();
        return $scheduleDay ? $scheduleDay->is_optional : false;
    }


    public static function getMandatoryShift($date)
    {
        $scheduleDay = self::where('date', $date)->first();
        return $scheduleDay ? $scheduleDay->mandatory_shift : null;
    }
}

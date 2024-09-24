<?php

namespace App\Services;

use App\Models\ScheduleDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleManager
{
    public static function updateDay($date, $isInverted, $isOptional, $mandatoryShift)
    {
        $scheduleDay = ScheduleDay::updateOrCreate(
            ['date' => $date],
            [
                'is_inverted' => $isInverted,
                'is_optional' => $isOptional,
                'mandatory_shift' => $mandatoryShift ?: null,
            ]
        );

        // Forzar la recarga de los datos desde la base de datos
        $scheduleDay->refresh();

        return $scheduleDay;
    }
    public static function getMonthSchedule($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->addMonths(1)->endOfMonth();      // Final del segundo mes

        Log::info("Fetching month schedule", ['start' => $startDate, 'end' => $endDate]);

        $scheduleDays = ScheduleDay::whereBetween('date', [$startDate, $endDate])->get();

        Log::info("Fetched schedule days", ['count' => $scheduleDays->count(), 'days' => $scheduleDays->toArray()]);

        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $scheduleDay = $scheduleDays->first(function ($day) use ($dateString) {
                return $day->date->toDateString() === $dateString;
            });

            $calendar[] = [
                'date' => $dateString,
                'is_inverted' => $scheduleDay ? (bool)$scheduleDay->is_inverted : false,
                'is_optional' => $scheduleDay ? (bool)$scheduleDay->is_optional : false,
                'mandatory_shift' => $scheduleDay ? $scheduleDay->mandatory_shift : null,
            ];

            Log::info("Day data", [
                'date' => $dateString,
                'is_inverted' => $scheduleDay ? (bool)$scheduleDay->is_inverted : false,
                'is_optional' => $scheduleDay ? (bool)$scheduleDay->is_optional : false,
                'mandatory_shift' => $scheduleDay ? $scheduleDay->mandatory_shift : null,
                'raw_data' => $scheduleDay ? $scheduleDay->toArray() : 'No data'
            ]);

            $currentDate->addDay();
        }

        Log::info("Calendar generated", ['calendar' => $calendar]);

        return $calendar;
    }
}

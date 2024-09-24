<?php

namespace App\Http\Controllers;

use App\Services\ScheduleManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ScheduleCalendarController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $calendar = ScheduleManager::getMonthSchedule($date->year, $date->month);

        Log::info("Calendar data for view", ['calendar' => $calendar]);

        return view('schedule_calendar.index', compact('calendar', 'date'));
    }

    public function updateDay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'is_inverted' => 'required|boolean',
            'is_optional' => 'required|boolean',
            'mandatory_shift' => 'nullable|string|in:morning,afternoon,night,complete',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $scheduleDay = ScheduleManager::updateDay(
            $request->input('date'),
            $request->boolean('is_inverted'),
            $request->boolean('is_optional'),
            $request->input('mandatory_shift')
        );

        Log::info("Day updated in controller", ['scheduleDay' => $scheduleDay->toArray()]);

        return response()->json([
            'date' => $scheduleDay->date->toDateString(),
            'is_inverted' => (bool) $scheduleDay->is_inverted,
            'is_optional' => (bool) $scheduleDay->is_optional,
            'mandatory_shift' => $scheduleDay->mandatory_shift,
        ]);
    }
}

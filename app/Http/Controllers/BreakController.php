<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BreakLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    public function startBreak(Request $request)
    {
        $user = Auth::user();

        $existingBreak = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->first();

        if ($existingBreak && $existingBreak->actual_end_time === null) {
            return response()->json(['error' => 'You are already on a break.'], 400);
        }

        if ($existingBreak && $existingBreak->actual_end_time !== null) {
            return response()->json(['error' => 'You have already taken your break today.'], 400);
        }

        $breakLog = new BreakLog();
        $breakLog->user_id = $user->id;
        $breakLog->start_time = Carbon::now();
        $breakLog->expected_end_time = Carbon::now()->addMinutes(30);
        $breakLog->save();

        $user->is_on_break = true;
        $user->save();

        return response()->json(['message' => 'Break started successfully.', 'start_time' => $breakLog->start_time->toIso8601String()]);
    }

    public function endBreak(Request $request)
    {
        $user = Auth::user();
        $breakLog = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->whereNull('actual_end_time')
            ->first();

        if (!$breakLog) {
            return response()->json(['error' => 'No active break found.'], 400);
        }

        $now = Carbon::now();
        $breakLog->actual_end_time = $now;

        $overtime = $now->diffInSeconds($breakLog->expected_end_time, false);
        if ($overtime > 5) {
            $breakLog->overtime = $overtime;
        }

        $breakLog->save();

        $user->is_on_break = false;
        $user->save();

        return response()->json(['message' => 'Break ended successfully.', 'overtime' => $breakLog->overtime]);
    }

    public function getBreakStatus()
    {
        $user = Auth::user();
        $breakLog = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->first();

        if (!$breakLog || $breakLog->actual_end_time !== null) {
            return response()->json([
                'is_on_break' => false,
                'remaining_time' => 1800,
                'break_taken' => $breakLog !== null,
                'start_time' => null
            ]);
        }

        $now = Carbon::now();
        $remainingTime = $now->diffInSeconds($breakLog->expected_end_time, false);

        return response()->json([
            'is_on_break' => true,
            'remaining_time' => max($remainingTime, -5),
            'break_taken' => true,
            'start_time' => $breakLog->start_time->toIso8601String()
        ]);
    }
}

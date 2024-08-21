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

        if ($existingBreak) {
            return response()->json(['error' => 'You have already taken your break today.'], 400);
        }

        $breakLog = new BreakLog();
        $breakLog->user_id = $user->id;
        $breakLog->start_time = Carbon::now();
        $breakLog->expected_end_time = Carbon::now()->addMinutes(30);
        $breakLog->save();

        $user->is_on_break = true;
        $user->save();

        return response()->json(['message' => 'Break started successfully.']);
    }

    public function getBreakStatus()
    {
        $user = Auth::user();
        $breakLog = BreakLog::where('user_id', $user->id)
            ->whereDate('start_time', Carbon::today())
            ->first();

        if (!$breakLog) {
            return response()->json([
                'is_on_break' => false,
                'remaining_time' => 1800,
                'break_taken' => false
            ]);
        }

        $now = Carbon::now();
        $remainingTime = $now->diffInSeconds($breakLog->expected_end_time, false);

        if ($remainingTime <= 0) {
            $user->is_on_break = false;
            $user->save();

            if (!$breakLog->actual_end_time) {
                $breakLog->actual_end_time = $breakLog->expected_end_time;
                $breakLog->overtime = $now->diffInSeconds($breakLog->expected_end_time);
                $breakLog->save();
            }

            return response()->json([
                'is_on_break' => false,
                'remaining_time' => 0,
                'break_taken' => true,
                'overtime' => $breakLog->overtime
            ]);
        }

        return response()->json([
            'is_on_break' => true,
            'remaining_time' => $remainingTime,
            'break_taken' => true,
            'start_time' => $breakLog->start_time->toIso8601String()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupOperator;
use App\Models\Girl;
use App\Models\Platform;

class ExtController extends Controller
{
    public function loadGroup(Request $request)
    {
        $user = $request->user();
        $groupOperator = GroupOperator::where('user_id', $user->id)->first();

        if (!$groupOperator) {
            return response()->json(['message' => 'Group not found for this operator'], 404);
        }

        $group = $groupOperator->group()->with('girls', 'platforms')->first();

        return response()->json([
            'group' => $group,
            'shift' => $groupOperator->shift
        ]);
    }

    public function loadGirls(Request $request)
    {
        $user = $request->user();
        $groupOperator = GroupOperator::where('user_id', $user->id)->first();

        if (!$groupOperator) {
            return response()->json(['message' => 'Group not found for this operator'], 404);
        }

        $girls = $groupOperator->group->girls;

        return response()->json($girls);
    }

    public function loadPlatforms(Request $request)
    {
        $user = $request->user();
        $groupOperator = GroupOperator::where('user_id', $user->id)->first();

        if (!$groupOperator) {
            return response()->json(['message' => 'Group not found for this operator'], 404);
        }

        $platforms = $groupOperator->group->platforms;

        return response()->json($platforms);
    }
}

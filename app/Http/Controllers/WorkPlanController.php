<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkPlanController extends Controller
{
    public function myPlan()
    {
        $user = auth()->user();
        $workPlan = $user->workPlan; // Asumiendo que tienes una relación 'workPlan' en tu modelo User
        return view('operator.my_work_plan', compact('workPlan'));
    }
}

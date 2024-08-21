<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperativeReportController extends Controller
{
    public function myReports()
    {
        $user = auth()->user();
        $reports = $user->operativeReports; // Asumiendo que tienes una relación 'operativeReports' en tu modelo User
        return view('operator.my_operative_reports', compact('reports'));
    }
}

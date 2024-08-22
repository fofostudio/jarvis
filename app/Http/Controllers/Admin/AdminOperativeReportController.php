<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperativeReport;
use Illuminate\Http\Request;

class AdminOperativeReportController extends Controller
{
    public function index()
{
    $pendingCount = OperativeReport::whereNull('is_approved')->count();
    $rejectedCount = OperativeReport::where('is_approved', false)->count();
    $approvedCount = OperativeReport::where('is_approved', true)->count();
    $totalCount = OperativeReport::count();

    $pendingReports = OperativeReport::with(['user', 'group'])
        ->whereNull('is_approved')
        ->orderBy('report_date', 'desc')
        ->get();

    $reviewedReports = OperativeReport::with(['user', 'group'])
        ->whereNotNull('is_approved')
        ->orderBy('report_date', 'desc')
        ->paginate(15);

    return view('admin.operative-reports.index', compact(
        'pendingCount',
        'rejectedCount',
        'approvedCount',
        'totalCount',
        'pendingReports',
        'reviewedReports'
    ));
}

    public function show(OperativeReport $report)
    {
        return view('admin.operative-reports.show', compact('report'));
    }

    public function updateStatus(Request $request, OperativeReport $report)
    {
        $validatedData = $request->validate([
            'is_approved' => 'required|boolean',
            'auditor_comment' => 'nullable|string|max:500',
        ]);

        $report->is_approved = $validatedData['is_approved'];
        $report->auditor_comment = $validatedData['auditor_comment'];
        $report->save();

        return redirect()->route('operative-reports.index', $report)
            ->with('success', 'Estado del reporte actualizado exitosamente.');
    }

    public function destroy(OperativeReport $report)
    {
        $report->delete();
        return redirect()->route('operative-reports.index')
            ->with('success', 'Reporte eliminado exitosamente.');
    }
}

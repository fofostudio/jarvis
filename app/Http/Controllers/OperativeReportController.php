<?php

namespace App\Http\Controllers;

use App\Models\GroupOperator;
use App\Models\OperativeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperativeReportController extends Controller
{
    /**
     * Display a listing of the reports for the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myReports()
    {
        $user = auth()->user();
        $reports = $user->operativeReports()->with('group')->orderBy('report_date', 'desc')->take(5)->get();

        // Obtener el grupo asignado al operador actual
        $groupOperator = GroupOperator::where('user_id', $user->id)->first();
        $groupReports = [];

        if ($groupOperator) {
            $groupReports = OperativeReport::whereHas('user', function ($query) use ($groupOperator) {
                $query->where('group_id', $groupOperator->group_id);
            })->with(['user', 'group'])->orderBy('report_date', 'desc')->take(5)->get();
        }

        return view('operator.my_operative_reports', compact('reports', 'groupReports'));
    }

    /**
     * Display a listing of all reports (for admins/auditors).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = OperativeReport::with('user')->orderBy('report_date', 'desc')->get();
        return view('admin.operative_reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('operator.create_operative_report');
    }

    /**
     * Store a newly created report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'report_date' => 'required|date',
            'report_type' => 'required|in:manual,conversational',
            'report_content' => 'required_if:report_type,manual',
            'gentleman_code' => 'required_if:report_type,conversational|array',
            'lady_code' => 'required_if:report_type,conversational|array',
            'conversation_summary' => 'required_if:report_type,conversational|array',
        ]);

        $report = new OperativeReport();
        $report->user_id = auth()->id();
        $report->report_date = $request->report_date;
        $report->report_type = $request->report_type;
        $groupOperator = GroupOperator::where('user_id', auth()->id())->first();
        if ($groupOperator) {
            $report->group_id = $groupOperator->group_id;
        }

        if ($request->report_type === 'manual') {
            $report->content = $request->report_content;
        } else {
            $conversations = [];
            foreach ($request->gentleman_code as $index => $gentlemanCode) {
                $conversations[] = [
                    'gentleman_code' => $gentlemanCode,
                    'lady_code' => $request->lady_code[$index],
                    'summary' => $request->conversation_summary[$index],
                ];
            }
            $report->content = json_encode($conversations);
        }

        $report->save();

        return redirect()->route('my-operative-reports')->with('success', 'Reporte guardado exitosamente.');
    }
    /**
     * Display the specified report.
     *
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function show(OperativeReport $operativeReport)
    {
        $this->authorize('view', $operativeReport);
        return view('operator.show_operative_report', compact('operativeReport'));
    }

    /**
     * Show the form for editing the specified report.
     *
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function edit(OperativeReport $operativeReport)
    {
        $this->authorize('update', $operativeReport);
        return view('operator.edit_operative_report', compact('operativeReport'));
    }

    /**
     * Update the specified report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OperativeReport $operativeReport)
    {
        $this->authorize('update', $operativeReport);

        $validatedData = $request->validate([
            'report_date' => 'required|date',
            'report_type' => 'required|in:file,manual',
            'report_file' => 'sometimes|required_if:report_type,file|file|mimes:pdf,doc,docx',
            'report_content' => 'required_if:report_type,manual',
        ]);

        $operativeReport->report_date = $request->report_date;
        $operativeReport->report_type = $request->report_type;
        $groupOperator = GroupOperator::where('user_id', auth()->id())->first();
        if ($groupOperator) {
            $operativeReport->group_id = $groupOperator->group_id;
        }

        if ($request->report_type === 'file' && $request->hasFile('report_file')) {
            // Delete old file if exists
            if ($operativeReport->file_path) {
                Storage::delete($operativeReport->file_path);
            }
            $path = $request->file('report_file')->store('reports');
            $operativeReport->file_path = $path;
            $operativeReport->content = null;
        } elseif ($request->report_type === 'manual') {
            $operativeReport->content = $request->report_content;
            $operativeReport->file_path = null;
        }

        $operativeReport->save();

        return redirect()->route('my-operative-reports')->with('success', 'Reporte actualizado exitosamente.');
    }

    /**
     * Remove the specified report from storage.
     *
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(OperativeReport $operativeReport)
    {


        if ($operativeReport->file_path) {
            Storage::delete($operativeReport->file_path);
        }

        $operativeReport->delete();

        return redirect()->route('my-operative-reports')->with('success', 'Reporte eliminado exitosamente.');
    }

    /**
     * Approve or reject a report (for auditors).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function reviewReport(Request $request, OperativeReport $operativeReport)
    {
        $this->authorize('review', $operativeReport);

        $validatedData = $request->validate([
            'is_approved' => 'required|boolean',
            'auditor_comment' => 'nullable|string|max:500',
        ]);

        $operativeReport->is_approved = $request->is_approved;
        $operativeReport->auditor_comment = $request->auditor_comment;
        $operativeReport->save();

        return redirect()->route('admin.operative-reports.index')->with('success', 'Reporte revisado exitosamente.');
    }

    /**
     * Download the report file.
     *
     * @param  \App\Models\OperativeReport  $operativeReport
     * @return \Illuminate\Http\Response
     */
    public function downloadFile(OperativeReport $operativeReport)
    {
        $this->authorize('view', $operativeReport);

        if (!$operativeReport->file_path) {
            abort(404, 'File not found');
        }

        return Storage::download($operativeReport->file_path);
    }
}

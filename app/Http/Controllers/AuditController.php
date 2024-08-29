<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Girl;
use App\Models\Group;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with(['auditor', 'operator'])->latest()->paginate(15);
        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        $operators = User::where('role', 'operator')->get();
        $platforms = Platform::all();
        $groups = Group::all();

        return view('audits.create', compact('operators', 'platforms', 'groups'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'operator_id' => 'required|exists:users,id',
            'conversation_date' => 'required|date',
            'review_date' => 'required|date',
            'platform' => 'required|string',
            'group' => 'required|string',
            'dama_name' => 'required|string',
            'dama_id' => 'required|string',
            'client_name' => 'required|string',
            'client_id' => 'required|string',
            'client_status' => 'required|string',
            'interesting_greetings' => 'required|boolean',
            'conversation_flow' => 'required|boolean',
            'new_conversation_topics' => 'required|boolean',
            'sentence_structure' => 'required|boolean',
            'generates_love_bond' => 'required|boolean',
            'moderate_gift_request' => 'required|boolean',
            'material_sending' => 'required|boolean',
            'commits_profile' => 'required|boolean',
            'response_times' => 'required|boolean',
            'initiates_hot_chat' => 'required|boolean',
            'conversation_coherence' => 'required|boolean',
            'general_score' => 'required|numeric|min:0|max:10',
            'general_observation' => 'required|string',
            'recommendations' => 'nullable|string',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('screenshots')) {
            $screenshotPaths = [];
            foreach ($request->file('screenshots') as $screenshot) {
                $path = $screenshot->store('audit_screenshots', 'public');
                $screenshotPaths[] = $path;
            }
            $validatedData['screenshot_paths'] = $screenshotPaths;
        }

        $validatedData['auditor_id'] = auth()->id();

        Audit::create($validatedData);

        return redirect()->route('audits.index')->with('success', 'Auditoría creada exitosamente.');
    }

    public function show(Audit $audit)
    {
        return view('audits.show', compact('audit'));
    }
}

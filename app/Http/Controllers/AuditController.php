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
        $audits = Audit::with(['auditor', 'operator', 'girl', 'platform', 'group'])
            ->get();

        return view('audits.index', compact('audits'));
    }

    public function create()
    {
        $operators = User::where('role', 'operator')->get();
        $platforms = Platform::all();
        $groups = Group::all();
        $checklistItems = $this->getChecklistItems();

        return view('audits.create', compact('operators', 'platforms', 'groups', 'checklistItems'));
    }
    public function edit(Audit $audit)
    {
        $operators = User::where('role', 'operator')->get();
        $platforms = Platform::all();
        $groups = Group::all();
        $girls = Girl::all();
        $checklistItems = $this->getChecklistItems();

        return view('audits.edit', compact('audit', 'operators', 'platforms', 'groups', 'girls', 'checklistItems'));
    }

    public function update(Request $request, Audit $audit)
    {
        $validatedData = $request->validate([
            'checklist' => 'required|array',
            'checklist.*' => 'boolean',
            'general_score' => 'required|numeric|min:0|max:100',
            'general_observation' => 'required|string',
            'recommendations' => 'nullable|string',
        ]);

        $audit->update($validatedData);

        return redirect()->route('audits.index', $audit)->with('success', 'Auditoría actualizada exitosamente.');
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();

        return redirect()->route('audits.index')->with('success', 'Auditoría eliminada exitosamente.');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'operator_id' => 'required|exists:users,id',
            'conversation_date' => 'required|date',
            'review_date' => 'required|date',
            'platform_id' => 'required|exists:platforms,id',
            'group_id' => 'required|exists:groups,id',
            'girl_id' => 'required|exists:girls,internal_id',
            'client_name' => 'required|string',
            'client_id' => 'required|string',
            'client_status' => 'required|string',
            'checklist' => 'required|array',
            'checklist.*' => 'required|boolean',
            'general_score' => 'required|numeric|min:0|max:100',
            'general_observation' => 'required|string',
            'recommendations' => 'nullable|string',
            'screenshots' => 'nullable|json',
        ]);

        // Buscar la chica por su internal_id y obtener su id de la base de datos
        $girl = Girl::where('internal_id', $validatedData['girl_id'])->firstOrFail();
        $validatedData['girl_id'] = $girl->id;

        $validatedData['auditor_id'] = auth()->id();
        $validatedData['screenshots'] = json_decode($request->screenshots, true);

        // Asegurarse de que platform_id y group_id estén correctamente asignados
        $validatedData['platform_id'] = $request->input('platform_id');
        $validatedData['group_id'] = $request->input('group_id');

        Audit::create($validatedData);

        return redirect()->route('audits.index')->with('success', 'Auditoría creada exitosamente.');
    }

    private function getChecklistItems()
    {
        return [
            'interesting_greetings' => ['label' => 'Saludos interesantes', 'score' => 10],
            'conversation_flow' => ['label' => 'Flujo de conversación', 'score' => 10],
            'new_conversation_topics' => ['label' => 'Nuevos temas de conversación', 'score' => 10],
            'sentence_structure' => ['label' => 'Estructura de frases', 'score' => 10],
            'generates_love_bond' => ['label' => 'Genera vínculo amoroso', 'score' => 10],
            'moderate_gift_request' => ['label' => 'Petición moderada de regalos', 'score' => 10],
            'material_sending' => ['label' => 'Envío de material', 'score' => 10],
            'commits_profile' => ['label' => 'Compromete el perfil', 'score' => 5],
            'response_times' => ['label' => 'Tiempos de respuesta', 'score' => 10],
            'initiates_hot_chat' => ['label' => 'Inicia/incentiva chat caliente', 'score' => 10],
            'conversation_coherence' => ['label' => 'Coherencia en conversación', 'score' => 5],
        ];
    }

    public function show(Audit $audit)
    {
        $checklistItems = $this->getChecklistItems();
        return view('audits.show', compact('audit', 'checklistItems'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditDetail;
use App\Models\Girl;
use App\Models\Group;
use App\Models\GroupCategory;
use App\Models\GroupOperator;
use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditController extends Controller
{
    public function index()
    {
        $audits = Audit::with(['auditor', 'group', 'operator', 'auditDetails'])
            ->orderBy('audit_date', 'desc')
            ->paginate(20);

        $totalAudits = Audit::count();
        $averageScore = Audit::avg('total_score');
        $auditsToday = Audit::whereDate('audit_date', today())->count();

        // Esto puede ser una consulta pesada si tienes muchos registros
        $totalGirlsAudited = AuditDetail::distinct('girl_id')->count('girl_id');

        return view('audits.index', compact('audits', 'totalAudits', 'averageScore', 'auditsToday', 'totalGirlsAudited'));
    }

    public function create()
    {
        // Obtener la categoría "Administración"
        $adminCategory = GroupCategory::where('name', 'Administracion')
            ->orderBy('name', 'asc')->first();

        // Filtrar los grupos que no pertenecen a la categoría "Administración"
        $groups = Group::where('group_category_id', '!=', $adminCategory->id) ->orderBy('name', 'asc')->get();
        // Obtener operadores que tienen el rol "operator" y excluir los que empiezan con "000"
        $operators = User::where('role', 'operator')
            ->where('name', 'not like', '000%') // Reemplaza 'username' por el campo adecuado
            ->orderBy('name', 'asc') // Ordenar alfabéticamente por username (de A a Z)

            ->get();
        $platforms = Platform::all();
        $checklistItems = $this->getChecklistItems();

        return view('audits.create', compact('groups', 'operators', 'platforms', 'checklistItems'));
    }

    public function store(Request $request)
    {
        Log::info('Iniciando proceso de creación de auditoría');
        Log::info('Datos recibidos:', $request->all());

        try {
            $validatedData = $request->validate([
                'audit_type' => 'required|in:group,individual',
                'group_id' => 'required_if:audit_type,group|exists:groups,id',
                'operator_id' => 'required_if:audit_type,individual|exists:users,id',
                'audit_date' => 'required|date',
                'audit_details' => 'required|array',
                'audit_details.*' => 'required|array',
                'audit_details.*.girl_id' => 'required|exists:girls,id',
                'audit_details.*.platform_id' => 'required|exists:platforms,id',
                'audit_details.*.client_name' => 'required|string',
                'audit_details.*.client_id' => 'required|string',
                'audit_details.*.client_status' => 'required|in:Nuevo,Antiguo',
                'audit_details.*.checklist' => 'required|array',
                'audit_details.*.general_score' => 'required|numeric|min:0|max:100',
                'audit_details.*.general_observation' => 'required|string',
                'audit_details.*.recommendations' => 'nullable|string',
                'audit_details.*.screenshots' => 'nullable|json',
            ]);

            Log::info('Datos validados correctamente', $validatedData);

            DB::beginTransaction();
            Log::info('Iniciando transacción de base de datos');

            $audit = Audit::create([
                'auditor_id' => auth()->id(),
                'audit_type' => $validatedData['audit_type'],
                'group_id' => $validatedData['audit_type'] == 'group' ? $validatedData['group_id'] : null,
                'operator_id' => $validatedData['audit_type'] == 'individual' ? $validatedData['operator_id'] : null,
                'audit_date' => $validatedData['audit_date'],
            ]);

            Log::info('Auditoría principal creada:', $audit->toArray());

            $totalScore = 0;
            $detailCount = 0;

            foreach ($validatedData['audit_details'] as $detailData) {
                Log::info("Procesando detalle para la chica ID: {$detailData['girl_id']}", $detailData);

                $auditDetail = new AuditDetail([
                    'girl_id' => $detailData['girl_id'],
                    'platform_id' => $detailData['platform_id'],
                    'client_name' => $detailData['client_name'],
                    'client_id' => $detailData['client_id'],
                    'client_status' => $detailData['client_status'],
                    'checklist' => $detailData['checklist'],
                    'general_score' => $detailData['general_score'],
                    'general_observation' => $detailData['general_observation'],
                    'recommendations' => $detailData['recommendations'] ?? null,
                    'screenshots' => $detailData['screenshots'] ?? null,
                ]);

                $auditDetail->audit_id = $audit->id;
                $auditDetail->operator_id = $validatedData['audit_type'] == 'individual' ? $validatedData['operator_id'] : null;
                $auditDetail->save();

                Log::info("Detalle de auditoría guardado:", $auditDetail->toArray());

                $totalScore += $auditDetail->general_score;
                $detailCount++;
            }

            $averageScore = $detailCount > 0 ? $totalScore / $detailCount : 0;
            $audit->update(['total_score' => $averageScore]);

            Log::info("Puntuación total actualizada. Promedio: $averageScore");

            DB::commit();
            Log::info('Transacción de base de datos completada');

            Log::info('Auditoría creada exitosamente');
            return redirect()->route('audits.index')->with('success', 'Auditoría creada exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la auditoría:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Error al crear la auditoría: ' . $e->getMessage())->withInput();
        }
    }
    public function show(Audit $audit)
    {
        $audit->load(['auditor', 'group', 'operator', 'auditDetails.girl', 'auditDetails.platform']);
        $checklistItems = $this->getChecklistItems();

        return view('audits.show', compact('audit', 'checklistItems'));
    }
    public function edit(Audit $audit)
    {
        $audit->load(['auditor', 'group', 'operator', 'auditDetails.girl', 'auditDetails.platform']);
        $checklistItems = $this->getChecklistItems();

        return view('audits.edit', compact('audit', 'checklistItems'));
    }

    public function update(Request $request, Audit $audit)
    {
        $validatedData = $request->validate([
            'audit_date' => 'required|date',
            'audit_details' => 'required|array',
            'audit_details.*.id' => 'required|exists:audit_details,id',
            'audit_details.*.platform_id' => 'required|exists:platforms,id',
            'audit_details.*.client_name' => 'required|string',
            'audit_details.*.client_id' => 'required|string',
            'audit_details.*.client_status' => 'required|in:Nuevo,Antiguo',
            'audit_details.*.checklist' => 'required|array',
            'audit_details.*.general_score' => 'required|numeric|min:0|max:100',
            'audit_details.*.general_observation' => 'required|string',
            'audit_details.*.recommendations' => 'nullable|string',
            'audit_details.*.screenshots' => 'nullable|json',
        ]);

        DB::beginTransaction();

        try {
            $audit->update([
                'audit_date' => $validatedData['audit_date'],
            ]);

            $totalScore = 0;

            foreach ($validatedData['audit_details'] as $detailData) {
                $auditDetail = AuditDetail::findOrFail($detailData['id']);
                $auditDetail->update($detailData);

                $totalScore += $auditDetail->general_score;
            }

            $audit->update(['total_score' => $totalScore / count($validatedData['audit_details'])]);

            DB::commit();

            return redirect()->route('audits.show', $audit)->with('success', 'Auditoría actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la auditoría: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Audit $audit)
    {
        try {
            $audit->auditDetails()->delete();
            $audit->delete();
            return redirect()->route('audits.index')->with('success', 'Auditoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la auditoría: ' . $e->getMessage());
        }
    }

    public function getOperatorsByGroup(Request $request)
    {
        $groupId = $request->input('group_id');
        $operators = User::where('role', 'operator')->where('group_id', $groupId)->get();
        return response()->json($operators);
    }

    public function getGirlsByGroup(Request $request)
    {
        $auditType = $request->input('audit_type');
        $groupId = $request->input('group_id');
        $operatorId = $request->input('operator_id');

        if ($auditType === 'individual') {
            // Para auditorías individuales, obtenemos el grupo del operador
            $groupOperator = GroupOperator::where('user_id', $operatorId)->first();
            if ($groupOperator) {
                $groupId = $groupOperator->group_id;
            }
        }

        $girls = Girl::with('platform')
            ->where('group_id', $groupId)
            ->get()
            ->map(function ($girl) {
                return [
                    'id' => $girl->id,
                    'name' => $girl->name,
                    'platform_id' => $girl->platform_id,
                    'platform_name' => $girl->platform ? $girl->platform->name : null
                ];
            });

        return response()->json($girls);
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
}

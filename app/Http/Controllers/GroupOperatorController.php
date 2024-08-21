<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupOperatorController extends Controller
{
    public function edit($id)
    {
        $assignment = GroupOperator::findOrFail($id);
        $groups = Group::all();
        $operators = User::where('role', 'operator')->get();

        return view('group_operator.edit', compact('assignment', 'groups', 'operators'));
    }

    public function update(Request $request, $id)
    {
        $assignment = GroupOperator::findOrFail($id);

        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
            'shift' => 'required|in:morning,afternoon,night',
        ]);

        DB::beginTransaction();

        try {
            // Verificar si ya existe una asignación para este grupo y turno
            $existingAssignment = GroupOperator::where('group_id', $request->group_id)
                ->where('shift', $request->shift)
                ->where('id', '!=', $id)
                ->first();

            if ($existingAssignment) {
                throw new \Exception('Ya existe un operador asignado a este grupo y turno.');
            }

            $assignment->update([
                'group_id' => $request->group_id,
                'user_id' => $request->user_id,
                'shift' => $request->shift,
            ]);

            DB::commit();

            return redirect()->route('group_operator.index')
                ->with('success', 'Asignación actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        $assignments = GroupOperator::with(['group', 'user'])->paginate(50);
        return view('group_operator.index', compact('assignments'));
    }
    public function create()
    {
        $shifts = ['morning', 'afternoon', 'night'];
        $selectedShift = request('shift');
        $groups = collect();
        $operators = collect();


    if ($selectedShift) {
        // Obtener IDs de grupos ya asignados para esta jornada
        $assignedGroupIds = GroupOperator::where('shift', $selectedShift)->pluck('group_id');

        // Obtener grupos disponibles
        $groups = Group::whereNotIn('id', $assignedGroupIds)->get();

        // Obtener IDs de operadores ya asignados en cualquier jornada
        $assignedOperatorIds = GroupOperator::pluck('user_id');

        // Obtener operadores disponibles que no tienen ningún grupo asignado
        $operators = User::where('role', 'operator')
                         ->whereNotIn('id', $assignedOperatorIds)
                         ->get();
    }

        return view('group_operator.create', compact('shifts', 'selectedShift', 'groups', 'operators'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'shift' => 'required|in:morning,afternoon,night',
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            // Verificar si ya existe una asignación para este grupo y turno
            $existingGroupAssignment = GroupOperator::where('group_id', $request->group_id)
                ->where('shift', $request->shift)
                ->first();

            if ($existingGroupAssignment) {
                throw new \Exception('Ya existe un operador asignado a este grupo y turno.');
            }

            // Verificar si el operador ya está asignado en este turno
            $existingOperatorAssignment = GroupOperator::where('user_id', $request->user_id)
                ->where('shift', $request->shift)
                ->first();

            if ($existingOperatorAssignment) {
                throw new \Exception('Este operador ya está asignado a un grupo en este turno.');
            }

            GroupOperator::create($request->all());

            DB::commit();

            return redirect()->route('group_operator.index')
                ->with('success', 'Asignación creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $assignment = GroupOperator::findOrFail($id);
        $assignment->delete();

        return redirect()->route('group_operator.index')
            ->with('success', 'Asignación eliminada exitosamente.');
    }
}

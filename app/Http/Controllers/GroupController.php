<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::paginate(30);
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Group::create($validated);

        return redirect()->route('groups.index')->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->update($validated);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }
    public function LoadExt(Request $request)
    {
        $user = $request->user();
        $group = $user->group;

        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        return response()->json($group);
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
    public function assignOperatorsForm()
    {
        $groups = Group::all();
        $operators = User::where('role', 'operator')->get();

        return view('groups.assign_operators', compact('groups', 'operators'));
    }

    public function assignOperators(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.group_id' => 'required|exists:groups,id',
            'assignments.*.operator_id' => 'required|exists:users,id',
            'assignments.*.shift' => 'required|in:morning,afternoon,night',
        ]);

        foreach ($validated['assignments'] as $assignment) {
            $group = Group::find($assignment['group_id']);
            $group->operators()->attach($assignment['operator_id'], ['shift' => $assignment['shift']]);
        }

        return redirect()->route('groups.index')->with('success', 'Operators assigned successfully.');
    }
}

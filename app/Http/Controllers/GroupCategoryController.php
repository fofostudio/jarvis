<?php

namespace App\Http\Controllers;

use App\Models\GroupCategory;
use Illuminate\Http\Request;

class GroupCategoryController extends Controller
{
    public function index()
    {
        $categories = GroupCategory::orderBy('order')->get();
        return view('group-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('group-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
            'monthly_goal' => 'required|integer',
            'task_description' => 'required|string',
        ]);

        GroupCategory::create($validated);

        return redirect()->route('group-categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(GroupCategory $groupCategory)
    {
        return view('group-categories.edit', compact('groupCategory'));
    }

    public function update(Request $request, GroupCategory $groupCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
            'monthly_goal' => 'required|integer',
            'task_description' => 'required|string',
        ]);

        $groupCategory->update($validated);

        return redirect()->route('group-categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(GroupCategory $groupCategory)
    {
        $groupCategory->delete();
        return redirect()->route('group-categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }

    public function showPoints(GroupCategory $groupCategory)
    {
        $totalPoints = $groupCategory->calculateTotalPoints();
        return view('group-categories.points', compact('groupCategory', 'totalPoints'));
    }
}

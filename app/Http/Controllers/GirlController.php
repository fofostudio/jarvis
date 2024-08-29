<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use App\Models\Platform;
use App\Models\Group;
use Illuminate\Http\Request;

class GirlController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->input('term');

        $girls = Girl::where('name', 'LIKE', "%$term%")
                     ->orWhere('username', 'LIKE', "%$term%")
                     ->orWhere('internal_id', 'LIKE', "%$term%")
                     ->get(['id', 'name', 'username', 'internal_id']);

        return response()->json($girls);
    }
    public function index(Request $request)
    {
        $query = Girl::with(['platform', 'group']);

        // Aplicar filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('internal_id', 'LIKE', "%{$search}%");
            });
        }


        // Aplicar filtro de plataforma
        if ($request->has('platform')) {
            $query->where('platform_id', $request->input('platform'));
        }

        // Aplicar filtro de grupo
        if ($request->has('group')) {
            $query->where('group_id', $request->input('group'));
        }

        $girls = $query->paginate(30);

        $groups = Group::withCount('girls')->get();
        $platforms = Platform::withCount('girls')->get();

        return view('girls.index', compact('girls', 'groups', 'platforms'));
    }


    public function create()
    {
        $platforms = Platform::all();
        $groups = Group::all();
        return view('girls.create', compact('platforms', 'groups'));
    }
    public function LoadExt()
    {
        $girls = Girl::all();
        return response()->json($girls);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'internal_id' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'platform_id' => 'required|exists:platforms,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        Girl::create($validated);

        return redirect()->route('girls.index')->with('success', 'Girl created successfully.');
    }

    public function show(Girl $girl)
    {
        return view('girls.show', compact('girl'));
    }

    public function edit(Girl $girl)
    {
        $platforms = Platform::all();
        $groups = Group::all();
        return view('girls.edit', compact('girl', 'platforms', 'groups'));
    }

    public function update(Request $request, Girl $girl)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'internal_id' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'platform_id' => 'required|exists:platforms,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $girl->update($validated);

        return redirect()->route('girls.index')->with('success', 'Girl updated successfully.');
    }

    public function destroy(Girl $girl)
    {
        $girl->delete();

        return redirect()->route('girls.index')->with('success', 'Girl deleted successfully.');
    }
}

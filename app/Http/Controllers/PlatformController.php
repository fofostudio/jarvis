<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::paginate(20);
        return view('platforms.index', compact('platforms'));
    }

    public function create()
    {
        return view('platforms.create');
    }
    public function LoadExt()
    {
        $platforms = Platform::all();
        return response()->json($platforms);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'access_mode' => 'required|in:multi_panel,simple',
            'color' => 'required|string|max:7',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $logoPath = $request->file('logo')->store('logos', 'public');
        $validated['logo'] = $logoPath;

        Platform::create($validated);

        return redirect()->route('platforms.index')->with('success', 'Platform created successfully.');
    }

    public function show(Platform $platform)
    {
        return view('platforms.show', compact('platform'));
    }

    public function edit(Platform $platform)
    {
        return view('platforms.edit', compact('platform'));
    }

    public function update(Request $request, Platform $platform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'access_mode' => 'required|in:multi_panel,simple',
            'color' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            Storage::disk('public')->delete($platform->logo);
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $platform->update($validated);

        return redirect()->route('platforms.index')->with('success', 'Platform updated successfully.');
    }

    public function destroy(Platform $platform)
    {
        Storage::disk('public')->delete($platform->logo);
        $platform->delete();

        return redirect()->route('platforms.index')->with('success', 'Platform deleted successfully.');
    }
}

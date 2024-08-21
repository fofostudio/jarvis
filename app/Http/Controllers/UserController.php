<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'operator')->paginate(40);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'identification' => 'required|string|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;

        }else {
            $validated['avatar'] = 'https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png?20150327203541';
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['entry_date'] = now(); // Establece la fecha de ingreso como la fecha y hora actuales
        $validated['role'] = 'operator';

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,operator',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'identification' => 'required|string|max:255|unique:users,identification,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
        ];

        // Solo validar la contraseña si se proporciona
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Actualizar la contraseña solo si se proporciona una nueva
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            // Eliminar el campo de contraseña del array $validated si está vacío
            unset($validated['password']);
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    public function destroy(User $user)
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}

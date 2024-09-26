<?php

namespace App\Http\Controllers;

use App\Models\GroupOperator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'operator')
            ->where('email', '!=', 'noname@mail.com') // Excluir el usuario con este correo
            ->orderBy('name', 'asc')
            ->paginate(40);
        $groupOperators = GroupOperator::with('user')->get();

        return view('users.index', compact('users', 'groupOperators'));
    }
    public function indexadmin()
    {
        $users = User::whereIn('role', ['coperative', 'admin', 'coordinador'])->orderBy('name', 'asc')->paginate(40);
        return view('users.index-admin', compact('users'));
    }
    public function createadmin()
    {
        return view('users.create-admin');
    }
    public function storeadmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'identification' => 'required|string|max:255|unique:users',
            'birth_date' => 'date',
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'neighborhood' => 'string|max:255',
            'role' => 'required',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        } else {
            $validated['avatar'] = asset('images/default-avatar.png');
        }

        $validated['password'] = Hash::make('TeamSic2024');
        $validated['entry_date'] = now(); // Establece la fecha de ingreso como la fecha y hora actuales

        User::create($validated);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
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
            'password' => 'string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'identification' => 'required|string|max:255|unique:users',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        } else {
            $validated['avatar'] = asset('images/default-avatar.png');
        }

        $validated['password'] = Hash::make('TeamSic2024');
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
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'identification' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'birth_date' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->identification = $request->identification;
        $user->birth_date = $request->birth_date;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->neighborhood = $request->neighborhood;

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('users.index', $user)->with('success', __('admin.user_updated_successfully'));
    }
    public function destroy(User $user)
{
    try {
        DB::beginTransaction();

        // Eliminar puntos asociados
        $user->points()->delete();

        // Eliminar registros de sesión
        $user->sessionLogs()->delete();

        // Eliminar registros de descanso
        $user->breakLogs()->delete();

        // Eliminar informes operativos
        $user->operativeReports()->delete();

        // Eliminar informes auditados
        $user->auditedReports()->delete();

        // Eliminar asociaciones de grupo
        $user->groupOperators()->delete();

        // Eliminar ventas asociadas
        $user->salesAsResponsible()->delete();

        // Eliminar pagos asociados
        $user->payments()->delete();

        // Eliminar balance del operador
        $user->operatorBalance()->delete();

        // Eliminar avatar si existe
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Finalmente, eliminar el usuario
        $user->delete();

        DB::commit();

        return redirect()->route('users.index')->with('success', 'User and all associated data deleted successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('users.index')->with('error', 'Failed to delete user. Error: ' . $e->getMessage());
    }
}
}

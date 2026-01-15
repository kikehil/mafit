<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plaza;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('plazaRef', 'modules');

        // Búsqueda por nombre, email o teléfono
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plazas = Plaza::orderBy('plaza')->get();
        $modules = Module::where('is_active', true)->orderBy('order')->get();
        return view('admin.users.create', compact('plazas', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'],
            'plaza' => ['required', 'string', 'exists:plazas,plaza'],
            'role' => ['required', 'string', Rule::in(['admin', 'supervisor', 'tecnico'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Asignar módulos si se proporcionaron
        if ($request->has('modules')) {
            $user->modules()->sync($request->modules);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $plazas = Plaza::orderBy('plaza')->get();
        $modules = Module::where('is_active', true)->orderBy('order')->get();
        $userModules = $user->modules->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'plazas', 'modules', 'userModules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'plaza' => ['required', 'string', 'exists:plazas,plaza'],
            'role' => ['required', 'string', Rule::in(['admin', 'supervisor', 'tecnico'])],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Si se proporciona nueva contraseña, actualizarla
        if ($request->filled('new_password')) {
            $validated['password'] = Hash::make($validated['new_password']);
        }

        // Remover new_password del array si existe
        unset($validated['new_password']);

        $user->update($validated);

        // Actualizar módulos asignados
        if ($request->has('modules')) {
            $user->modules()->sync($request->modules);
        } else {
            // Si no se proporcionan módulos y no es admin, eliminar todos
            if (!$user->isAdmin()) {
                $user->modules()->detach();
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // No permitir eliminar al usuario actual
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tienda;
use Illuminate\Http\Request;

class AdminTiendaAssignmentController extends Controller
{
    /**
     * Mostrar listado de usuarios para asignar tiendas
     */
    public function index()
    {
        $users = User::with('plazaRef', 'tiendas')
            ->whereNotNull('plaza')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.tienda-assignment.index', compact('users'));
    }

    /**
     * Mostrar formulario de asignación de tiendas para un usuario
     */
    public function edit(User $user)
    {
        if (!$user->plaza) {
            return redirect()->route('admin.tienda-assignment.index')
                ->with('error', 'El usuario no tiene una plaza asignada.');
        }

        // Obtener IDs de tiendas asignadas a otros usuarios de la misma plaza (excluyendo al usuario actual)
        $tiendasAsignadasAOtros = Tienda::where('plaza', $user->plaza)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id)
                      ->where('users.plaza', $user->plaza);
            })
            ->pluck('id')
            ->toArray();

        // Obtener tiendas de la plaza del usuario que:
        // - Comienzan con "50"
        // - NO están asignadas a otros usuarios de la misma plaza
        // - Ordenadas por nombre
        $tiendas = Tienda::where('plaza', $user->plaza)
            ->where('cr', 'like', '50%')
            ->whereNotIn('id', $tiendasAsignadasAOtros)
            ->orderBy('tienda')
            ->get();

        // Obtener IDs de tiendas ya asignadas al usuario actual
        $tiendasAsignadas = $user->tiendas->pluck('id')->toArray();

        return view('admin.tienda-assignment.edit', compact('user', 'tiendas', 'tiendasAsignadas'));
    }

    /**
     * Actualizar asignación de tiendas para un usuario
     */
    public function update(Request $request, User $user)
    {
        if (!$user->plaza) {
            return redirect()->route('admin.tienda-assignment.index')
                ->with('error', 'El usuario no tiene una plaza asignada.');
        }

        $request->validate([
            'tiendas' => ['nullable', 'array'],
            'tiendas.*' => ['exists:tiendas,id'],
        ]);

        // Verificar que las tiendas pertenezcan a la plaza del usuario y comiencen con "50"
        if ($request->has('tiendas')) {
            $tiendasIds = $request->input('tiendas', []);
            $tiendasValidas = Tienda::where('plaza', $user->plaza)
                ->where('cr', 'like', '50%')
                ->whereIn('id', $tiendasIds)
                ->pluck('id')
                ->toArray();

            if (count($tiendasIds) !== count($tiendasValidas)) {
                return redirect()->back()
                    ->with('error', 'Algunas tiendas seleccionadas no pertenecen a la plaza del usuario.');
            }

            $user->tiendas()->sync($tiendasValidas);
        } else {
            // Si no se seleccionó ninguna tienda, eliminar todas las asignaciones
            $user->tiendas()->detach();
        }

        return redirect()->route('admin.tienda-assignment.index')
            ->with('success', 'Asignación de tiendas actualizada exitosamente.');
    }
}

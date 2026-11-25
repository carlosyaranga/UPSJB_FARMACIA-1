<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Listado de roles
    public function index()
    {
        $roles = Role::latest()->get();

        return view('settings.roles.index', compact('roles'));
    }

    // Formulario para crear
    public function create()
    {
        return view('settings.roles.create');
    }

    // Guardar nuevo rol
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:roles,nombre',
            'description' => 'nullable|max:255',
        ]);

        Role::create([
            'nombre' => $validated['name'],
            'descripcion' => $validated['description'] ?? null,
        ]);

        return to_route('roles.index')->with('ok', 'Rol registrado');
    }

    // Formulario para editar
    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    // Actualizar rol
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:50', Rule::unique('roles', 'nombre')->ignore($role->id)],
            'description' => 'nullable|max:255',
        ]);

        $role->update([
            'nombre' => $validated['name'],
            'descripcion' => $validated['description'] ?? null,
        ]);

        return to_route('roles.index')->with('ok', 'Rol actualizado');
    }

    // Eliminar rol (solo si no tiene usuarios)
    public function destroy(Role $role)
    {
        if ($role->usuarios()->exists()) {
            return to_route('roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados');
        }

        $role->delete();

        return to_route('roles.index')->with('ok', 'Rol eliminado');
    }
}

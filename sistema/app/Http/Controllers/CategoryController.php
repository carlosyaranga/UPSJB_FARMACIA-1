<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    // Listado de categorías (las más recientes primero)
    public function index()
    {
        $categories = Categoria::latest()->get();

        return view('settings.categories.index', compact('categories'));
    }

    // Formulario para crear
    public function create()
    {
        return view('settings.categories.create');
    }

    // Guardar nueva categoría
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categorias,nombre',
            'description' => 'nullable|max:255',
        ]);

        Categoria::create([
            'nombre' => $validated['name'],
            'descripcion' => $validated['description'] ?? null,
        ]);

        // Patrón PRG: se redirige al listado con un mensaje flash
        return to_route('categories.index')->with('ok', 'Categoría registrada');
    }

    // Formulario para editar
    public function edit(Category $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    // Actualizar categoría (el nombre sigue siendo único, ignorando la propia)
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('categorias', 'nombre')->ignore($category->id),
            ],
            'description' => 'nullable|max:255',
        ]);

        $category->update([
            'nombre' => $validated['name'],
            'descripcion' => $validated['description'] ?? null,
        ]);

        return to_route('categories.index')->with('ok', 'Categoría actualizada');
    }

    // Eliminar categoría (solo si no tiene medicamentos)
    public function destroy(Category $category)
    {
        // Regla del diagrama: la FK es restrictOnDelete(); se avisa antes de que MySQL lance el error 1451
        if ($category->medicines()->exists()) {
            return to_route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene medicamentos asociados');
        }

        $category->delete();

        return to_route('categories.index')->with('ok', 'Categoría eliminada');
    }
}

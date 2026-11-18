<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Categoria::all();

        return view('settings.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('settings.categories.create');
    }

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

        return redirect()->route('categories.index')
            ->with('success', 'Categoría registrada correctamente.');
    }

    public function show(Categoria $category)
    {
        return view('settings.categories.show', compact('category'));
    }

    public function edit(Categoria $category)
    {
        return view('settings.categories.edit', compact('category'));
    }

    public function update(Request $request, Categoria $category)
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

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}

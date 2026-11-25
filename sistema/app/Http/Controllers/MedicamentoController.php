<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class MedicamentoController extends Controller
{
    public function index(Request $r)
    {
        $medicamentos = Medicamento::with('categoria')->when($r->filled('buscar'), fn ($q) => $q->where('nombre', 'like', '%'.$r->buscar.'%'))->orderBy('nombre')->paginate(10)->withQueryString();

        return view('medicamentos.index', compact('medicamentos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('medicamentos.create', compact('categorias'));
    }

    public function store(Request $r)
    {
        $d = $r->validate(['categoria_id' => 'required|exists:categorias,id', 'nombre' => 'required|string|max:120', 'precio' => 'required|numeric|min:0.01', 'stock' => 'required|integer|min:0']);
        Medicamento::create($d);

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento registrado correctamente.');
    }

    public function edit(Medicamento $medicamento)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('medicamentos.edit', compact('medicamento', 'categorias'));
    }

    public function update(Request $r, Medicamento $medicamento)
    {
        $d = $r->validate(['categoria_id' => 'required|exists:categorias,id', 'nombre' => 'required|string|max:120', 'precio' => 'required|numeric|min:0.01', 'stock' => 'required|integer|min:0']);
        $medicamento->update($d);

        return redirect()->route('medicamentos.index')->with('success', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicamento $medicamento)
    {
        if ($medicamento->detallesVenta()->exists()) {
            return back()->with('error', 'No se puede eliminar: ya aparece en ventas.');
        } $medicamento->delete();

        return back()->with('success','Medicamento eliminado correctamente.');
    }
}

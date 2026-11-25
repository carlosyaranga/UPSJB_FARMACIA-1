<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $r)
    {
        $clientes = Cliente::when($r->filled('buscar'), fn ($q) => $q->where(fn ($s) => $s->where('nombre', 'like', '%'.$r->buscar.'%')->orWhere('dni', 'like', '%'.$r->buscar.'%')))->orderBy('nombre')->paginate(10)->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $r)
    {
        $d = $r->validate(['nombre' => 'required|string|max:150', 'dni' => 'required|digits:8|unique:clientes,dni', 'telefono' => 'nullable|string|max:15', 'edad' => 'nullable|integer|min:0|max:120']);
        Cliente::create($d);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $r, Cliente $cliente)
    {
        $d = $r->validate(['nombre' => 'required|string|max:150', 'dni' => ['required', 'digits:8', Rule::unique('clientes', 'dni')->ignore($cliente->id)], 'telefono' => 'nullable|string|max:15', 'edad' => 'nullable|integer|min:0|max:120']);
        $cliente->update($d);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene ventas registradas.');
        } $cliente->delete();

        return back()->with('success','Cliente eliminado correctamente.');
    }
}

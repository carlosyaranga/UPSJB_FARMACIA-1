<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        return view('settings.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('settings.customer.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'dni' => 'required|max:15|unique:clientes,dni',
            'telefono' => 'nullable|max:20',
            'edad' => 'nullable|integer|min:0|max:120',
        ]);

        Customer::create($validated);

        return to_route('customers.index')->with('ok', 'Cliente registrado');
    }

    public function show(Customer $customer)
    {
        return view('settings.customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('settings.customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:100',
            'dni' => [
                'required',
                'max:15',
                Rule::unique('clientes', 'dni')->ignore($customer->id),
            ],
            'telefono' => 'nullable|max:20',
            'edad' => 'nullable|integer|min:0|max:120',
        ]);

        $customer->update($validated);

        return to_route('customers.index')->with('ok', 'Cliente actualizado');
    }

    // Eliminar cliente (solo si no tiene ventas)
    public function destroy(Customer $customer)
    {
        if ($customer->ventas()->exists()) {
            return to_route('customers.index')->with('error', 'No se puede eliminar el cliente porque tiene ventas registradas');
        }

        $customer->delete();

        return to_route('customers.index')->with('ok', 'Cliente eliminado');
    }
}

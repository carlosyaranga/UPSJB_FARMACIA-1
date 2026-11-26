<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest('date')->get();

        return view('management.sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $medicines = $this->availableMedicines();

        return view('management.sales.create', compact('customers', 'medicines'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSale($request);

        $sale = DB::transaction(function () use ($validated, $request) {
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => $request->user()->id,
                'date' => now(),
                'total' => 0,
                'igv' => 0,
                'status' => 'active',
                'cancelled_at' => null,
            ]);

            $total = $this->processItems($sale, $validated['items']);

            $sale->update([
                'total' => round($total, 2),
                'igv' => round($total * 18 / 118, 2),
            ]);

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta registrada');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.medicine']);

        return view('management.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->isCancelled()) {
            return to_route('sales.index')->with('error', 'No puede editar una venta anulada');
        }

        $customers = Customer::orderBy('name')->get();
        $medicines = $this->availableMedicines($sale);
        $sale->load('details.medicine');

        return view('management.sales.edit', compact('sale', 'customers', 'medicines'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->isCancelled()) {
            return to_route('sales.index')->with('error', 'No puede modificar una venta anulada');
        }

        $validated = $this->validateSale($request);

        DB::transaction(function () use ($sale, $validated) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            foreach ($sale->details as $detail) {
                $medicine = Medicine::lockForUpdate()->findOrFail($detail->medicine_id);
                $medicine->increment('stock', $detail->amount);
            }

            $sale->details()->delete();

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'total' => 0,
                'igv' => 0,
            ]);

            $total = $this->processItems($sale, $validated['items']);

            $sale->update([
                'total' => round($total, 2),
                'igv' => round($total * 18 / 118, 2),
            ]);
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta actualizada y stock ajustado');
    }

    public function destroy(Request $request, Sale $sale)
    {
        abort_unless($request->user()->hasRole('Administrador'), 403);

        if ($sale->isCancelled()) {
            return to_route('sales.index')->with('error', 'Esta venta ya se encuentra anulada');
        }

        if (! $sale->date || now()->diffInHours($sale->date) > 24) {
            return to_route('sales.index')->with('error', 'Solo se pueden anular ventas registradas en las últimas 24 horas');
        }

        DB::transaction(function () use ($sale) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            foreach ($sale->details as $detail) {
                $medicine = Medicine::lockForUpdate()->findOrFail($detail->medicine_id);
                $medicine->increment('stock', $detail->amount);
            }

            $sale->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        });

        return to_route('sales.index')->with('ok', 'Venta anulada y stock devuelto');
    }

    private function validateSale(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|distinct|exists:medicines,id',
            'items.*.amount' => 'required|integer|min:1|max:4294967295',
        ], [
            'items.required' => 'Debe agregar al menos un medicamento.',
            'items.*.medicine_id.distinct' => 'No repita el mismo medicamento, aumente la cantidad.',
            'items.*.amount.min' => 'La cantidad debe ser al menos 1.',
        ]);
    }

    private function availableMedicines(?Sale $sale = null)
    {
        $query = Medicine::where(function ($query) {
            $query->where('stock', '>', 0)
                ->orWhere(function ($query) {
                    $query->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', now()->toDateString());
                });
        });

        if ($sale) {
            $detailMedicineIds = $sale->details()->pluck('medicine_id');
            $query->where(function ($query) use ($detailMedicineIds) {
                $query->where('stock', '>', 0)->orWhereIn('id', $detailMedicineIds);
            });
        } else {
            $query->where('stock', '>', 0)
                ->where(function ($query) {
                    $query->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', now()->toDateString());
                });
        }

        return $query->orderBy('name')->get();
    }

    private function processItems(Sale $sale, array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $medicine = Medicine::lockForUpdate()->findOrFail($item['medicine_id']);
            $expiryDate = $medicine->getRawOriginal('expiry_date');

            if ($expiryDate && $expiryDate < now()->toDateString()) {
                throw ValidationException::withMessages([
                    'items' => "El medicamento {$medicine->name} se encuentra vencido.",
                ]);
            }

            if ($medicine->stock < (int) $item['amount']) {
                throw ValidationException::withMessages([
                    'items' => "Stock insuficiente para {$medicine->name} (disponible: {$medicine->stock}).",
                ]);
            }

            $subtotal = (float) $medicine->price * (int) $item['amount'];

            $sale->details()->create([
                'medicine_id' => $medicine->id,
                'amount' => (int) $item['amount'],
                'price_unit' => $medicine->price,
                'subtotal' => $subtotal,
            ]);

            $medicine->decrement('stock', (int) $item['amount']);
            $total += $subtotal;
        }

        return $total;
    }
}

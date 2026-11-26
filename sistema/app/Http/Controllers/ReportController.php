<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $sales = Sale::with(['customer', 'user'])
            ->when($request->filled('from'), fn ($query) => $query->whereDate('date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('date', '<=', $request->input('to')))
            ->latest('date')
            ->get();

        return view('principal.reports.index', [
            'sales' => $sales,
            'total' => $sales->sum('total'),
        ]);
    }

    public function salesPdf(Request $request)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $sales = Sale::with(['customer', 'user'])
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('date', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('date', '<=', $to))
            ->latest('date')
            ->get();

        return Pdf::loadView('principal.reports.pdf.sales', [
            'sales' => $sales,
            'total' => $sales->sum('total'),
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
        ])
            ->setPaper('a4', 'portrait')
            ->stream('reporte-ventas.pdf');
    }

    public function inventoryPdf()
    {
        $medicines = Medicine::with('category')->orderBy('name')->get();

        return Pdf::loadView('principal.reports.pdf.inventory', compact('medicines'))
            ->setPaper('a4', 'portrait')
            ->stream('reporte-inventario.pdf');
    }

    public function lowStockPdf()
    {
        $medicines = Medicine::with('category')->where('stock', '<=', 10)->orderBy('stock')->get();

        return Pdf::loadView('principal.reports.pdf.low-stock', compact('medicines'))
            ->setPaper('a4', 'portrait')
            ->stream('reporte-stock-bajo.pdf');
    }

    public function expiryPdf()
    {
        $medicines = Medicine::with('category')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
            ->orderBy('expiry_date')
            ->get();

        return Pdf::loadView('principal.reports.pdf.expiry', compact('medicines'))
            ->setPaper('a4', 'portrait')
            ->stream('reporte-vencimientos.pdf');
    }

    public function customersPdf()
    {
        $customers = Customer::withCount('sales')->orderBy('name')->get();

        return Pdf::loadView('principal.reports.pdf.customers', compact('customers'))
            ->setPaper('a4', 'portrait')
            ->stream('reporte-clientes.pdf');
    }

    public function usersPdf()
    {
        $users = User::with('role')->orderBy('name')->get();

        return Pdf::loadView('principal.reports.pdf.users', compact('users'))
            ->setPaper('a4', 'portrait')
            ->stream('reporte-usuarios.pdf');
    }
}

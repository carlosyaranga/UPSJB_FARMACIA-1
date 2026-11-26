<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Exports\MedicinesExport;
use App\Exports\SalesExport;
use App\Imports\MedicinesImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function index(): View
    {
        return view('principal.reports.excel');
    }

    public function medicines(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        $import = new MedicinesImport;
        Excel::import($import, $request->file('file'));

        return back()->with('ok', "Importación finalizada: {$import->imported} medicamentos registrados; {$import->errorsCount()} errores.")
            ->with('import_errors', $import->errors);
    }

    public function exportMedicines()
    {
        return Excel::download(new MedicinesExport, 'medicamentos.xlsx');
    }

    public function exportCustomers()
    {
        return Excel::download(new CustomersExport, 'clientes.xlsx');
    }

    public function exportSales(Request $request)
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        return Excel::download(new SalesExport($validated['from'] ?? null, $validated['to'] ?? null), 'ventas.xlsx');
    }
}

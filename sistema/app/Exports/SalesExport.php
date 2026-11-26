<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly ?string $from = null, private readonly ?string $to = null) {}

    public function collection(): Enumerable
    {
        return Sale::with(['customer', 'user'])
            ->when($this->from, fn ($query) => $query->whereDate('date', '>=', $this->from))
            ->when($this->to, fn ($query) => $query->whereDate('date', '<=', $this->to))
            ->latest('date')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Cliente', 'Vendedor', 'Subtotal', 'IGV', 'Total'];
    }

    public function map($sale): array
    {
        $base = (float) $sale->total - (float) $sale->igv;

        return [$sale->id, $sale->date->format('Y-m-d H:i'), $sale->customer?->name, $sale->user?->name, number_format($base, 2, '.', ''), $sale->igv, $sale->total];
    }
}

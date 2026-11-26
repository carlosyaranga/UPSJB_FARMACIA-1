<?php

namespace App\Exports;

use App\Models\Medicine;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MedicinesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Enumerable
    {
        return Medicine::with('category')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Categoría', 'Nombre', 'Precio', 'Stock', 'Vencimiento'];
    }

    public function map($medicine): array
    {
        return [
            $medicine->id,
            $medicine->category?->name,
            $medicine->name,
            $medicine->price,
            $medicine->stock,
            optional($medicine->expiry_date)->format('Y-m-d'),
        ];
    }
}

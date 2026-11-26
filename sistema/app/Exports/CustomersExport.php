<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Enumerable
    {
        return Customer::withCount('sales')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'DNI', 'Teléfono', 'Edad', 'Compras'];
    }

    public function map($customer): array
    {
        return [$customer->id, $customer->name, $customer->dni, $customer->phone, $customer->age, $customer->sales_count];
    }
}

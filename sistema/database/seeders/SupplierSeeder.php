<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    // Registra proveedores ficticios para pruebas y demostraciones.
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Distribuidora Farma Salud S.A.C.',
                'ruc' => '20100000001',
                'phone' => '987654321',
                'email' => 'ventas@farmasalud.test',
                'address' => 'Av. Los Olivos 120, Lima',
            ],
            [
                'name' => 'Laboratorios Vida Nueva S.A.',
                'ruc' => '20100000002',
                'phone' => '912345678',
                'email' => 'contacto@vidanueva.test',
                'address' => 'Jr. Las Flores 450, Lima',
            ],
            [
                'name' => 'Insumos Médicos del Perú S.R.L.',
                'ruc' => '20100000003',
                'phone' => '923456789',
                'email' => 'pedidos@insumosperu.test',
                'address' => 'Calle Salud 88, Arequipa',
            ],
            [
                'name' => 'Comercializadora Botica Central S.A.C.',
                'ruc' => '20100000004',
                'phone' => '934567891',
                'email' => 'comercial@boticacentral.test',
                'address' => 'Av. Central 760, Trujillo',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}

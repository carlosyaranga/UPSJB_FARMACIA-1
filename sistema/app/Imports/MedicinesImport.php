<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicinesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $name = trim((string) ($row['nombre'] ?? ''));
            $categoryName = trim((string) ($row['categoria'] ?? ''));
            $price = (float) ($row['precio'] ?? -1);
            $stock = (int) ($row['stock'] ?? -1);
            $expiry = trim((string) ($row['vencimiento'] ?? ''));

            if ($name === '' || $categoryName === '' || $price < 0 || $stock < 0) {
                $this->errors[] = "Fila {$rowNumber}: nombre, categoría, precio o stock inválidos.";

                continue;
            }

            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                $this->errors[] = "Fila {$rowNumber}: categoría '{$categoryName}' no existe.";

                continue;
            }

            if ($expiry !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry)) {
                $this->errors[] = "Fila {$rowNumber}: vencimiento debe tener formato AAAA-MM-DD.";

                continue;
            }

            Medicine::create([
                'category_id' => $category->id,
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'expiry_date' => $expiry !== '' ? $expiry : null,
            ]);

            $this->imported++;
        }
    }

    public function errorsCount(): int
    {
        return count($this->errors);
    }
}

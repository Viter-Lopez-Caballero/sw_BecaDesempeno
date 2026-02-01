<?php

namespace App\Imports;

use App\Models\PriorityArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PriorityAreasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Assuming 'nombre' or 'name' column
        // Check for duplicates?
        if (PriorityArea::where('name', $row['nombre'] ?? $row['name'])->exists()) {
            return null;
        }

        return new PriorityArea([
            'name' => $row['nombre'] ?? $row['name'],
            'description' => $row['descripcion'] ?? $row['description'] ?? null,
        ]);
    }
}

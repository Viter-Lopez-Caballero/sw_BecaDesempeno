<?php

namespace App\Imports;

use App\Models\Institucion;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InstitutionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Try to find state by name or ID if provided, otherwise default or null
        // Assuming Excel has 'nombre', 'estado' columns.
        $estadoId = null;
        if (!empty($row['estado'])) {
            $estado = Estado::where('nombre', 'LIKE', '%' . $row['estado'] . '%')->first();
            $estadoId = $estado ? $estado->id : null;
        }

        return new Institucion([
            'nombre'     => $row['nombre'], 
            'estado_id'  => $estadoId ?? 1, // Defaulting to 1 if not found, or nullable? Model says nullable? No, likely required.
        ]);
    }
}

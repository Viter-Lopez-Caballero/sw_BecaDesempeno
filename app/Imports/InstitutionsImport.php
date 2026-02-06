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
        // Las columnas del Excel son: 'nombre_de_la_institucion', 'ciudad', 'estado'
        $estadoId = null;
        if (!empty($row['estado'])) {
            $estado = Estado::where('nombre', 'LIKE', '%' . trim($row['estado']) . '%')->first();
            $estadoId = $estado ? $estado->id : null;
        }

        return new Institucion([
            'nombre'     => $row['nombre_de_la_institucion'] ?? $row['nombre'], 
            'estado_id'  => $estadoId ?? 1,
        ]);
    }
}

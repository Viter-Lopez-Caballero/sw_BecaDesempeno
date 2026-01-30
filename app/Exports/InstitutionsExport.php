<?php

namespace App\Exports;

use App\Models\Institucion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InstitutionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Institucion::with('estado')->get();
    }

    public function map($institution): array
    {
        return [
            $institution->id,
            $institution->nombre,
            $institution->estado ? $institution->estado->nombre : '',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Estado',
        ];
    }
}

<?php

namespace App\Http\Resources\Catalogos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConvocatoriaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'anio' => $this->anio,
            'estado' => $this->estado,
            'archivo_path' => $this->archivo_path,
            'archivo_nombre' => $this->archivo_nombre,
            'archivo_tipo' => $this->archivo_tipo,
            'archivo_size' => $this->archivo_size,
            'calendario' => $this->whenLoaded('calendario'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

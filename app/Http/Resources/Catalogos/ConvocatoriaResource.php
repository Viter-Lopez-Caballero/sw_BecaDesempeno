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
            'estado' => $this->estado,
            'imagen_path' => $this->imagen_path,
            'imagen_url' => $this->imagen_path ? asset('storage/' . $this->imagen_path) : null,
            'archivo_path' => $this->archivo_path,
            'archivo_url' => $this->archivo_path ? asset('storage/' . $this->archivo_path) : null,
            'archivo_nombre' => $this->archivo_nombre,
            'archivo_tipo' => $this->archivo_tipo,
            'archivo_size' => $this->archivo_size,
            'calendario' => $this->relationLoaded('calendario') && $this->calendario ? new CalendarioResource($this->calendario) : null,
            'fecha_fin' => $this->when(
                $this->relationLoaded('calendario'),
                fn() => $this->calendario?->registro_fin ?? null
            ),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

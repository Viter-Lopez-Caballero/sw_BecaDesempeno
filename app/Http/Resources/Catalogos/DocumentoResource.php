<?php

namespace App\Http\Resources\Catalogos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentoResource extends JsonResource
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
            'archivo_path' => $this->archivo_path,
            'archivo_nombre' => $this->archivo_nombre,
            'archivo_tipo' => $this->archivo_tipo,
            'archivo_size' => $this->archivo_size,
            'activo' => $this->activo,
            'es_fundamental' => $this->es_fundamental,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            // Pivot data when loaded via convocatoria relationship
            'es_obligatorio' => $this->whenPivotLoaded('convocatoria_documento', function () {
                return (bool) $this->pivot->es_obligatorio;
            }),
            // URL de plantilla para descarga (si tiene archivo)
            'url_plantilla' => $this->archivo_path ? "/storage/{$this->archivo_path}" : null,
        ];
    }
}

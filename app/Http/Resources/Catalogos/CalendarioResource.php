<?php

namespace App\Http\Resources\Catalogos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarioResource extends JsonResource
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
            'convocatoria_id' => $this->convocatoria_id,
            'convocatoria' => $this->whenLoaded('convocatoria'),
            'publicacion_inicio' => $this->publicacion_inicio?->format('Y-m-d'),
            'registro_inicio' => $this->registro_inicio?->format('Y-m-d'),
            'registro_fin' => $this->registro_fin?->format('Y-m-d'),
            'evaluacion_inicio' => $this->evaluacion_inicio?->format('Y-m-d'),
            'evaluacion_fin' => $this->evaluacion_fin?->format('Y-m-d'),
            'resultados_inicio' => $this->resultados_inicio?->format('Y-m-d'),
            'resultados_fin' => $this->resultados_fin?->format('Y-m-d'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

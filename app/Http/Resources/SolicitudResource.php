<?php

namespace App\Http\Resources;

use App\Http\Resources\Catalogos\ConvocatoriaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
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
            'user_id' => $this->user_id,
            'convocatoria_id' => $this->convocatoria_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships (conditional)
            'user' => new UserResource($this->whenLoaded('user')),
            'convocatoria' => $this->when(
                $this->relationLoaded('convocatoria'),
                fn() => $this->convocatoria ? new ConvocatoriaResource($this->convocatoria) : null
            ),
            'documentos' => $this->when(
                $this->relationLoaded('documentos'),
                fn() => DocumentoResource::collection($this->documentos)->resolve()
            ),
            'evaluaciones' => $this->whenLoaded('evaluaciones'),
            
            // Computeds
            'documentos_count' => $this->whenCounted('documentos'),
            
            // Computed fields for SuperAdmin Documentos view
            'profesor' => $this->when(
                $this->relationLoaded('user'),
                fn() => $this->user ? [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'departamento' => $this->user->priorityArea?->name ?? null,
                    'estado' => $this->user->institucion?->estado?->nombre ?? null,
                ] : null
            ),
            'campus' => $this->when(
                $this->relationLoaded('user'),
                fn() => $this->user?->institucion?->nombre ?? 'N/A'
            ),
        ];
    }
}

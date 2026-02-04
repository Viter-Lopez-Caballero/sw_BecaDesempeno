<?php

namespace App\Http\Resources;

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
            'status' => $this->status, // Assuming there is a status column, though we removed it from Index view, it might be relevant.
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships (conditional)
            'user' => new UserResource($this->whenLoaded('user')),
            'convocatoria' => $this->whenLoaded('convocatoria'), // Or ConvocatoriaResource if it exists
            'documentos' => DocumentoResource::collection($this->whenLoaded('documentos')),
            'evaluaciones' => $this->whenLoaded('evaluaciones'),
            
            // Computeds
            'documentos_count' => $this->whenCounted('documentos'),
        ];
    }
}

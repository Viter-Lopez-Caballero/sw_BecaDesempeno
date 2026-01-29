<?php

namespace App\Http\Resources\Catalogos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionResource extends JsonResource
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
            'estado_id' => $this->estado_id,
            'estado' => $this->whenLoaded('estado'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

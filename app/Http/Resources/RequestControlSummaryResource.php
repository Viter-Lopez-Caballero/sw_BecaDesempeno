<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestControlSummaryResource extends JsonResource
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
            'estado' => $this->whenLoaded('estado', function () {
                return $this->estado->nombre;
            }),
            'approved_count' => $this->approved_count ?? 0, // Using the aggregate aliases
            'rejected_count' => $this->rejected_count ?? 0,
        ];
    }
}

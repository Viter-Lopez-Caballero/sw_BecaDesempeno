<?php

namespace App\Http\Resources;

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
            'solicitud_id' => $this->solicitud_id,
            'name' => $this->name,
            'file_path' => $this->file_path,
            'file_type' => $this->file_type,
            'created_at' => $this->created_at,
        ];
    }
}

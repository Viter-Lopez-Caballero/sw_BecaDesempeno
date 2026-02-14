<?php

namespace App\Http\Resources;

// use App\Http\Resources\Catalogos\ConvocatoriaResource; // Removed
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'announcement_id' => $this->announcement_id,
            'status' => $this->status,
            'admin_comment' => $this->admin_comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships (conditional)
            'user' => new UserResource($this->whenLoaded('user')),
            'announcement' => $this->announcement ? (new \App\Http\Resources\Catalog\AnnouncementResource($this->announcement))->resolve() : null, // Uses AnnouncementResource
            'documents' => $this->when(
                $this->relationLoaded('documents'),
                fn() => \App\Http\Resources\DocumentResource::collection($this->documents)->resolve() // Uses DocumentResource
            ),
            'evaluations' => $this->whenLoaded('evaluations'), // Maybe EvaluationResource later?
            
            // Computeds
            'documents_count' => $this->whenCounted('documents'),
            
            // Computed fields for SuperAdmin Documents view
            'teacher' => $this->user ? [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'department' => $this->user->priorityArea?->name ?? null, // priorityArea renamed? yes.
                'state' => $this->user->institution?->state?->name ?? null, // institucion -> institution
            ] : null,
            'campus' => $this->when(
                $this->relationLoaded('user'),
                fn() => $this->user?->institution?->name ?? 'N/A' // nombre -> name
            ),
        ];
    }
}

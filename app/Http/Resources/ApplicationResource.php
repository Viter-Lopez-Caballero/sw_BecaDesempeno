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
        $user = auth()->user();
        $isTeacher = $user && $user->hasRole('Docente');
        
        // Determine whether to mask the status and comment for the Teacher
        $status = $this->status;
        $adminComment = $this->admin_comment;
        
        if ($isTeacher && $this->announcement) {
            $stage = $this->announcement->current_stage;
            if (!in_array($stage, ['resultados', 'terminada'])) {
                // Si aún no son resultados, el docente solo ve "pendiente" para no arruinar la sorpresa
                if ($status !== 'draft') { // si fuera draft, sigue siendo draft (aunque docente no tiene drafts, pero por si acaso)
                    $status = 'pending';
                }
                $adminComment = null;
            }
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'announcement_id' => $this->announcement_id,
            'status' => $status,
            'position_type_id' => $this->position_type_id,
            'position_type_name' => $this->positionType?->name,
            'position_type_code' => $this->positionType?->code,
            'position_full_type' => $this->positionType 
                ? "{$this->positionType->code} - {$this->positionType->name}" 
                : null,
            'admin_comment' => $adminComment,
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
                'department' => $this->user->priorityArea?->name ?? null,
                'sub_area' => $this->user->subArea?->name ?? null,
                'state' => $this->user->institution?->state?->name ?? null, // institucion -> institution
            ] : null,
            'campus' => $this->when(
                $this->relationLoaded('user'),
                fn() => $this->user?->institution?->name ?? 'N/A' // nombre -> name
            ),
        ];
    }
}

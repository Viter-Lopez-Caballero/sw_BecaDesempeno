<?php

namespace App\Http\Resources;

use App\Traits\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use DateFormat;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'role'          => $this->getPrimaryRole(),
            'roles'         => RoleResource::collection($this->whenLoaded('roles')),
            'roles_ids'     => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('id')->toArray();
            }),
            'is_active'     => $this->is_active,
            'created_at'    => $this->textFormatDate($this->created_at),
            'deleted_at'    => $this->deleted_at ? $this->textFormatDate($this->deleted_at) : null,
            'institucion'   => $this->whenLoaded('institucion'),
            'priority_area' => $this->whenLoaded('priorityArea'),
            'sub_area'      => $this->whenLoaded('subArea'),
        ];
    }
}

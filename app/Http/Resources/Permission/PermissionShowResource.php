<?php

namespace App\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionShowResource extends JsonResource
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
            'title' => $this->title,
            'module' => $this->module,
            'action' => $this->action,
            'resource' => $this->resource,
            'description' => $this->description,
            'is_active' => $this->is_active
        ];
    }
}

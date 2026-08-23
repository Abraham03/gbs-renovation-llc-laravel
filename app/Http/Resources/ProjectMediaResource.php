<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
{
    return [
        'id' => $this->id,
        'type' => $this->type,
        // Generamos la URL absoluta
        'url' => $this->url ? url(Storage::url(str_replace('storage/', '', $this->url))) : null,
    ];
}
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            // 1. Estandarizamos a camelCase para Angular y aseguramos ruta absoluta
            'thumbnailUrl' => asset($this->thumbnail_url), 
            
            // 2. Simplificamos la categoría para mandar solo el nombre en texto
            'category' => $this->category ? $this->category->name : 'Sin categoría',
            
            // 3. Mapeamos la galería multimedia
            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($item) {
                    return [
                        'id' => $item->id,         // <-- ¡Agregamos el ID aquí!
                        'type' => $item->type,
                        'url' => asset($item->url)
                    ];
                });
            })
        ];
    }
}
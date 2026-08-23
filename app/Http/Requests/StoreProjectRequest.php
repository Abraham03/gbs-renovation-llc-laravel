<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Asumimos que el middleware JWT ya validó al usuario
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            
            // Aumentamos el límite del thumbnail a 10MB (10240 KB)
            'thumbnail'   => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', 
            
            'media'       => 'nullable|array',
            
            // 50MB para los archivos de la galería (para que soporten videos sin problema)
            'media.*'     => 'file|mimes:jpeg,png,jpg,webp,mp4,mov,avi|max:51200', 
        ];
    }
}
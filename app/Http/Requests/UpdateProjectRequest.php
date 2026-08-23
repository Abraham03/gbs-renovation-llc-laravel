<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|exists:categories,id',
            'title'       => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            // La imagen es opcional (nullable) al actualizar
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', 
        ];
    }
}
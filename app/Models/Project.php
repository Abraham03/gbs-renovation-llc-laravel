<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'thumbnail_url',
        'description'
    ];

    // Relación: Un proyecto pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación: Un proyecto tiene muchos archivos multimedia
    public function media()
    {
        return $this->hasMany(ProjectMedia::class);
    }
}
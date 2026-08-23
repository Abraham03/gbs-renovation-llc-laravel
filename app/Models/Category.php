<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Campos permitidos para inserción masiva
    protected $fillable = ['name'];

    // Relación: Una categoría tiene muchos proyectos
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
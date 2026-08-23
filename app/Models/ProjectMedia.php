<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMedia extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'url'
    ];

    // Relación: Un archivo multimedia pertenece a un proyecto
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
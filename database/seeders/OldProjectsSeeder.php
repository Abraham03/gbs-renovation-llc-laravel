<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class OldProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Leer el archivo JSON
        $jsonPath = database_path('data/projects.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("El archivo JSON no existe en: {$jsonPath}");
            return;
        }

        $jsonFile = File::get($jsonPath);
        $projects = json_decode($jsonFile, true);

        // 2. Recorrer cada proyecto del JSON
        foreach ($projects as $item) {
            
            // Buscar o crear la categoría basada en el JSON
            $category = Category::firstOrCreate([
                'name' => $item['category']
            ]);

            // Mapeo exacto a las columnas de tu migración
            $project = Project::updateOrCreate(
                ['title' => $item['title']], 
                [
                    'category_id' => $category->id,
                    // AQUÍ ESTÁ LA MAGIA: Traducimos del JSON a tu Base de Datos
                    'thumbnail_url' => $item['thumbnailUrl'], 
                    'description' => $item['description'],
                ]
            );

            // 3. Insertar la galería (Media) en project_media
            if (isset($item['media']) && is_array($item['media'])) {
                foreach ($item['media'] as $mediaItem) {
                    $project->media()->updateOrCreate(
                        ['url' => $mediaItem['url']], 
                        ['type' => $mediaItem['type']]
                    );
                }
            }

            $this->command->info("Proyecto importado: " . $item['title']);
        }

        $this->command->info('¡Toda la base de datos se ha llenado con éxito!');
    }
}
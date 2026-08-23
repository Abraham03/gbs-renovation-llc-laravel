<?php

namespace App\Services;

use App\Models\Project;
use App\Jobs\ProcessVideoCompression;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format; // <-- Enum oficial de la V4
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class ProjectService
{
    public function createProjectWithMedia(array $data, UploadedFile $thumbnail, array $mediaFiles = [])
    {
        return DB::transaction(function () use ($data, $thumbnail, $mediaFiles) {
            
            $data['thumbnail_url'] = $this->processAndSaveImage($thumbnail, 'projects/thumbnails');
            
            $project = Project::create($data);

            if (!empty($mediaFiles)) {
                foreach ($mediaFiles as $file) {
                    $mimeType = $file->getMimeType();
                    $isImage = str_contains($mimeType, 'image');

                    if ($isImage) {
                        $url = $this->processAndSaveImage($file, 'projects/gallery');
                        $project->media()->create(['type' => 'image', 'url' => $url]);
                    } else {
                        $tempPath = $file->store('temp_videos', 'local');
                        $media = $project->media()->create([
                            'type' => 'video', 
                            'url' => 'processing...'
                        ]);
                        ProcessVideoCompression::dispatch($media->id, $tempPath);
                    }
                }
            }

            return $project;
        });
    }

    // 1. Actualiza esta función para que acepte los archivos extra
    public function updateProject(Project $project, array $data, ?UploadedFile $thumbnail = null, array $mediaFiles = [])
    {
        return DB::transaction(function () use ($project, $data, $thumbnail, $mediaFiles) {
            
            if ($thumbnail) {
                $oldPath = str_replace('storage/', '', $project->thumbnail_url);
                Storage::disk('public')->delete($oldPath);
                $data['thumbnail_url'] = $this->processAndSaveImage($thumbnail, 'projects/thumbnails');
            }

            $project->update($data);

            // Si se subieron nuevas fotos/videos al editar, las agregamos a la galería
            if (!empty($mediaFiles)) {
                foreach ($mediaFiles as $file) {
                    $mimeType = $file->getMimeType();
                    if (str_contains($mimeType, 'image')) {
                        $url = $this->processAndSaveImage($file, 'projects/gallery');
                        $project->media()->create(['type' => 'image', 'url' => $url]);
                    } else {
                        $tempPath = $file->store('temp_videos', 'local');
                        $media = $project->media()->create(['type' => 'video', 'url' => 'processing...']);
                        \App\Jobs\ProcessVideoCompression::dispatch($media->id, $tempPath);
                    }
                }
            }

            return $project;
        });
    }

    // 2. Agrega esta nueva función al final de tu clase ProjectService
    public function deleteMedia(int $mediaId)
    {
        // Buscamos el archivo directamente en la base de datos
        $media = DB::table('project_media')->where('id', $mediaId)->first();
        
        if ($media) {
            // Borramos del disco duro
            $mediaPath = str_replace('storage/', '', $media->url);
            Storage::disk('public')->delete($mediaPath);
            
            // Borramos de la base de datos
            DB::table('project_media')->where('id', $mediaId)->delete();
        }
    }

    public function deleteProject(Project $project)
    {
        DB::transaction(function () use ($project) {
            // 1. Borrar miniatura del disco
            $thumbnailPath = str_replace('storage/', '', $project->thumbnail_url);
            Storage::disk('public')->delete($thumbnailPath);

            // 2. Borrar todos los archivos de la galería del disco
            foreach ($project->media as $media) {
                $mediaPath = str_replace('storage/', '', $media->url);
                Storage::disk('public')->delete($mediaPath);
            }

            // 3. Eliminar de la base de datos (por la restricción en cascada, borrará los registros de project_media automáticamente)
            $project->delete();
        });
    }

    private function processAndSaveImage(UploadedFile $file, string $path): string
    {
        // 1. Instanciar ImageManager (Nueva sintaxis estricta de V4)
        $manager = ImageManager::usingDriver(Driver::class);
        
        // 2. Decodificar (V4 reemplazó read() por decode())
        $image = $manager->decode($file);
        
        // 3. Redimensionar (scaleDown evita pixelar si la imagen original es chica)
        $image->scaleDown(width: 1200);
        
        // 4. Codificar (V4 usa Enums de Formato para máxima seguridad)
        $encoded = $image->encodeUsingFormat(Format::WEBP, quality: 80);
        
        // 5. Guardar en el disco
        $filename = $path . '/' . uniqid() . '.webp';
        Storage::disk('public')->put($filename, $encoded->toString());

        return 'storage/' . $filename;
    }
}
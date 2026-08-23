<?php

namespace App\Jobs;

use App\Models\ProjectMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264; // <-- Formato estándar universal
use Illuminate\Support\Facades\Storage;

class ProcessVideoCompression implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $mediaId;
    protected string $tempPath;

    public function __construct(int $mediaId, string $tempPath)
    {
        $this->mediaId = $mediaId;
        $this->tempPath = $tempPath;
    }

    public function handle(): void
    {
        $media = ProjectMedia::find($this->mediaId);
        if (!$media) return;

        // Cambiamos la extensión final a .mp4
        $optimizedPath = 'projects/videos/optimized_' . time() . '.mp4';

        // Comprimimos usando el formato X264 estándar
        FFMpeg::fromDisk('local')
            ->open($this->tempPath)
            ->export()
            ->toDisk('public')
            ->inFormat(new X264()) 
            ->save($optimizedPath);

        $media->update([
            'url' => 'storage/' . $optimizedPath,
        ]);

        Storage::disk('local')->delete($this->tempPath);
    }
}
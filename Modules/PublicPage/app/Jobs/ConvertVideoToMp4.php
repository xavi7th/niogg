<?php

namespace Modules\PublicPage\Jobs;

use Exception;
use Modules\PublicPage\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ConvertVideoToMp4 implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const STORAGE_DISK = 'public';

    private const RETRY_AFTER = 3600; // 1 hour

    private const TIMEOUT = 1800; // 30 minutes

    private int $tries = 3;

    private int $timeout = self::TIMEOUT;

    public int $uniqueFor = 3600; // Prevent duplicate jobs within 1 hour

    public function __construct(public readonly Video $video)
    {
        $this->onQueue('video-conversion');
    }

    public function uniqueId(): string
    {
        return "video:{$this->video->id}:convert-to-mp4";
    }

    public function handle(): void
    {
        $video = Video::findOrFail($this->video->id);

        if ($video->mime_type === 'video/mp4') {
            return;
        }

        $video->update(['conversion_status' => 'converting']);

        $sourcePath = $this->getLocalPath($video->video_url);

        if (! file_exists($sourcePath)) {
            throw new Exception("Video file not found: {$sourcePath}");
        }

        $outputFilename = $this->generateOutputFilename();
        $outputPath = Storage::disk(self::STORAGE_DISK)->path("videos/{$outputFilename}");

        $format = new X264('aac', 'libx264');
        $format->setKiloBitrate(1000)
            ->setAudioKiloBitrate(128);

        FFMpeg::fromDisk('')
            ->open($sourcePath)
            ->export()
            ->toDisk(self::STORAGE_DISK)
            ->inFormat($format)
            ->save("videos/{$outputFilename}");

        $oldPath = $this->getRelativePath($video->video_url);

        $video->update([
            'video_url' => Storage::disk(self::STORAGE_DISK)->url("videos/{$outputFilename}"),
            'mime_type' => 'video/mp4',
            'conversion_status' => 'completed',
            'conversion_completed_at' => now(),
        ]);

        Storage::disk(self::STORAGE_DISK)->delete($oldPath);
    }

    public function failed(Exception $exception): void
    {
        $video = Video::find($this->video->id);

        if ($video) {
            $video->update([
                'conversion_status' => 'failed',
                'conversion_error' => $exception->getMessage(),
            ]);
        }
    }

    private function getLocalPath(string $url): string
    {
        $relativePath = $this->getRelativePath($url);

        return Storage::disk(self::STORAGE_DISK)->path($relativePath);
    }

    private function getRelativePath(string $url): string
    {
        $storageUrl = Storage::disk(self::STORAGE_DISK)->url('');

    $relativePath = str_replace($storageUrl, '', $url);

    if (strpos($url, 'http') === 0) {
      $relativePath = parse_url($url, PHP_URL_PATH);
      $relativePath = str_replace('/storage/', '', $relativePath);
    } else {
      $relativePath = ltrim(str_replace('/storage/', '', $relativePath), '/');
    }

    return $relativePath;
    }

    private function generateOutputFilename(): string
    {
        return str_replace('.mp4', '', $this->video->upload_id ?? uniqid()) . '_converted.mp4';
    }

    public function middleware(): array
    {
        return [new \Illuminate\Queue\Middleware\WithoutOverlapping("video:{$this->video->id}")];
    }
}

<?php

namespace Modules\PublicPage\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\PublicPage\Services\EventPhotoUploadService;

class GeneratePhotoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public EventPhoto $photo)
    {
    }

    public function handle(EventPhotoUploadService $service): void
    {
        $service->generateThumbnail($this->photo);
    }

    public function failed(Throwable $exception): void
    {
        $this->photo->update(['thumbnail_generation' => 'failed']);
    }
}

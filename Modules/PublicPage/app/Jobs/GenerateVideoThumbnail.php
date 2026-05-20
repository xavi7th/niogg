<?php

namespace Modules\PublicPage\Jobs;

use Illuminate\Bus\Queueable;
use Modules\PublicPage\Models\Video;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\PublicPage\Services\VideoThumbnailService;

class GenerateVideoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public Video $video)
    {
    }

    public function handle(VideoThumbnailService $service): void
    {
        $service->generateForVideo($this->video);
    }
}

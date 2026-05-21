<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;

class RetryFailedVideoThumbnails extends Command
{
    protected $signature = 'videos:retry-thumbnails';

    protected $description = 'Dispatch thumbnail generation for videos with no thumbnail';

    public function handle(): void
    {
        $count = Video::query()
            ->whereNull('thumbnail_url')
            ->whereNull('custom_thumbnail_url')
            ->count();

        if ($count === 0) {
            $this->info('No videos need thumbnail generation.');

            return;
        }

        $this->info('Dispatching thumbnail generation for ' . $count . ' video(s)...');

        Video::query()
            ->whereNull('thumbnail_url')
            ->whereNull('custom_thumbnail_url')
            ->each(function (Video $video): void {
                GenerateVideoThumbnail::dispatch($video);
                $this->line('  Dispatched for video ID ' . $video->id . ': ' . $video->title);
            });

        $this->info('Done.');
    }
}

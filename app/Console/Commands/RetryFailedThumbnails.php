<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;

class RetryFailedThumbnails extends Command
{
    protected $signature = 'photos:retry-failed-thumbnails';

    protected $description = 'Re-dispatch thumbnail generation for photos with missing or failed thumbnails';

    public function handle(): void
    {
        EventPhoto::query()
            ->where(function ($query): void {
                $query->whereNull('thumbnail_url')
                    ->orWhere('thumbnail_generation', 'failed');
            })
            ->each(fn (EventPhoto $photo) => GeneratePhotoThumbnail::dispatch($photo));
    }
}

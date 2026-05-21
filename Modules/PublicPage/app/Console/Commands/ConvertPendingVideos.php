<?php

namespace Modules\PublicPage\Console\Commands;

use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;

class ConvertPendingVideos extends Command
{
  protected $signature = 'videos:convert-pending {--dry-run : Show what would be done without dispatching jobs}';

  protected $description = 'Scan for non-MP4 videos and schedule conversion jobs';

  private int $scheduled = 0;

  private int $skipped = 0;

  private int $failed = 0;

  private int $alreadyCompleted = 0;

  public function handle(): int
  {
    $isDryRun = $this->option('dry-run');

    if ($isDryRun) {
      $this->info('DRY RUN MODE - No jobs will be dispatched');
    }

    $this->info('Scanning for videos that need conversion...');

    $videos = $this->findPendingVideos();

    if ($videos->isEmpty()) {
      $this->info('No videos found that need conversion.');

      return self::SUCCESS;
    }

    $this->newLine();
    $this->info('Found ' . $videos->count() . ' video(s) needing conversion:');
    $this->newLine();

    $this->processVideos($videos, $isDryRun);

    $this->printSummary();

    return self::SUCCESS;
  }

  private function findPendingVideos(): Collection
  {
    return Video::query()
        ->where('mime_type', '!=', 'video/mp4')
        ->where(function ($query): void {
          $query->whereNull('conversion_status')
              ->orWhere('conversion_status', 'pending');
        })
        ->get();
  }

  private function processVideos(Collection $videos, bool $isDryRun): void
  {
    foreach ($videos as $video) {
      $this->processVideo($video, $isDryRun);
    }
  }

  private function processVideo(Video $video, bool $isDryRun): void
  {
    $this->line('• <comment>' . $video->title . '</comment> (ID: ' . $video->id . ')');
    $this->line('  Format: ' . $video->mime_type);
    $this->line('  Status: ' . ($video->conversion_status ?? 'null'));
    $this->line('  URL: ' . $video->video_url);

    if ($this->videoFileExists($video)) {
      if ($isDryRun) {
        $this->info('  [DRY RUN] Would schedule conversion job');
        $this->scheduled++;
      } else {
        $this->scheduleConversion($video);
      }
    } else {
      $this->error('  File not found, skipping');
      $this->skipped++;
    }

    $this->newLine();
  }

  private function scheduleConversion(Video $video): void
  {
    try {
      Bus::dispatch(new ConvertVideoToMp4($video));
      $this->info('  Conversion job dispatched');
      $this->scheduled++;
    } catch (Throwable $e) {
      $this->error('  Failed to dispatch: ' . $e->getMessage());
      $this->failed++;
    }
  }

  private function videoFileExists(Video $video): bool
  {
    $storageUrl = Storage::disk('public')->url('');
    $videoUrl = $video->video_url;

    $relativePath = str_replace($storageUrl, '', $videoUrl);

    if (str_starts_with($videoUrl, 'http')) {
      $relativePath = parse_url($videoUrl, PHP_URL_PATH);
      $relativePath = str_replace('/storage/', '', $relativePath);
    } else {
      $relativePath = ltrim(str_replace('/storage/', '', $relativePath), '/');
    }

    return Storage::disk('public')->exists($relativePath);
  }

  private function printSummary(): void
  {
    $this->newLine();
    $this->info('=== Summary ===');
    $this->line('Scheduled: <info>' . $this->scheduled . '</info>');
    $this->line('Skipped (file not found): <comment>' . $this->skipped . '</comment>');
    $this->line('Failed to dispatch: <error>' . $this->failed . '</error>');
    $this->line('Already completed: ' . $this->alreadyCompleted);
  }
}

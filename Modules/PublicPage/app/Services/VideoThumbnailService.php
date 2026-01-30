<?php

namespace Modules\PublicPage\Services;

use FFMpeg\FFMpeg;
use Illuminate\Support\Str;
use InvalidArgumentException;
use FFMpeg\Coordinate\TimeCode;
use Intervention\Image\ImageManager;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class VideoThumbnailService
{
  private const STORAGE_DISK = 'public';

  private const THUMBNAIL_PATH = 'videos/thumbnails';

  private const THUMBNAIL_POSITION_PERCENT = 10; // Extract frame at 10% of video duration

  private readonly ImageManager $imageManager;

  /**
   * Thumbnail sizes (width x height)
   */
  private const SIZES = [
    'small' => [320, 180],
    'medium' => [640, 360],
    'large' => [1280, 720],
  ];

  public function __construct()
  {
    $this->imageManager = new ImageManager(new Driver());
  }

  /**
   * Generate thumbnails for a video file
   *
   * @param  string  $videoPath  Path to video file in storage
   * @param  int|null  $duration  Video duration in seconds (optional, will be extracted if not provided)
   * @return array URLs to generated thumbnails [small, medium, large]
   */
  public function generateFromPath(string $videoPath, ?int $duration = NULL): array
  {
    $fullVideoPath = Storage::disk(self::STORAGE_DISK)->path($videoPath);

    if ( ! file_exists($fullVideoPath)) {
      throw new InvalidArgumentException("Video file not found: {$videoPath}");
    }

    $ffmpeg = FFMpeg::create();
    $video = $ffmpeg->open($fullVideoPath);

    // Get duration if not provided
    if ($duration === NULL) {
      $duration = $this->getVideoDuration($video);
    }

    // Calculate time position (10% of video)
    $timePosition = (int) max(1, ($duration * self::THUMBNAIL_POSITION_PERCENT) / 100);

    // Extract frame at specified time position
    $frame = $video->frame(TimeCode::fromSeconds($timePosition));

    // Save to temporary file
    $tempFile = tempnam(sys_get_temp_dir(), 'thumb_');
    $frame->save($tempFile);

    // Generate thumbnails in all sizes
    $thumbnails = [];
    $thumbnailBaseName = Str::uuid()->toString();

    foreach (self::SIZES as $size => list($width, $height)) {
      $thumbnailFilename = "{$thumbnailBaseName}_{$size}.jpg";
      $thumbnailPath = self::THUMBNAIL_PATH . '/' . $thumbnailFilename;

      // Resize and save thumbnail
      $image = $this->imageManager->read($tempFile);
      $image->cover($width, $height);
      Storage::disk(self::STORAGE_DISK)->put(
          $thumbnailPath,
          $image->toJpeg(quality: 85)
      );

      $thumbnails[$size] = Storage::disk(self::STORAGE_DISK)->url($thumbnailPath);
    }

    // Clean up temp file
    if (file_exists($tempFile)) {
      unlink($tempFile);
    }

    return $thumbnails;
  }

  /**
   * Generate thumbnails for a Video model and update it
   *
   * @param  Video  $video  The video model
   * @return Video Updated video model with thumbnail_url
   */
  public function generateForVideo(Video $video): Video
  {
    // Extract relative path from full URL
    $videoPath = str_replace(
        Storage::disk(self::STORAGE_DISK)->url(''),
        '',
        $video->video_url
    );

    $thumbnails = $this->generateFromPath($videoPath, $video->duration_seconds);

    // Store the medium thumbnail as the main thumbnail
    $video->thumbnail_url = $thumbnails['medium'];
    $video->save();

    return $video;
  }

  /**
   * Get video duration in seconds
   */
  private function getVideoDuration($video): int
  {
    // FFmpeg way to get duration
    $format = $video->getFormat();
    $duration = $format->get('duration');

    return (int) $duration;
  }

  /**
   * Delete all thumbnails for a video
   */
  public function deleteThumbnails(?string $thumbnailUrl): void
  {
    if (empty($thumbnailUrl)) {
      return;
    }

    $thumbnailPath = str_replace(
        Storage::disk(self::STORAGE_DISK)->url(''),
        '',
        $thumbnailUrl
    );

    // Extract base name (without size suffix)
    $pathInfo = pathinfo($thumbnailPath);
    $baseName = str_replace('_medium', '', $pathInfo['filename']);

    // Delete all size variants
    foreach (self::SIZES as $size => list($width, $height)) {
      $sizePath = self::THUMBNAIL_PATH . '/' . $baseName . "_{$size}.jpg";
      if (Storage::disk(self::STORAGE_DISK)->exists($sizePath)) {
        Storage::disk(self::STORAGE_DISK)->delete($sizePath);
      }
    }
  }

  /**
   * Get all thumbnail URLs for a video
   *
   * @param  string|null  $thumbnailUrl  Main thumbnail URL (medium size)
   * @return array URLs for all sizes [small, medium, large]
   */
  public function getAllSizes(?string $thumbnailUrl): array
  {
    if (empty($thumbnailUrl)) {
      return [
        'small' => NULL,
        'medium' => NULL,
        'large' => NULL,
      ];
    }

    $thumbnailPath = str_replace(
        Storage::disk(self::STORAGE_DISK)->url(''),
        '',
        $thumbnailUrl
    );

    $pathInfo = pathinfo($thumbnailPath);
    $baseName = str_replace('_medium', '', $pathInfo['filename']);

    $urls = [];
    foreach (self::SIZES as $size => list($width, $height)) {
      $sizePath = self::THUMBNAIL_PATH . '/' . $baseName . "_{$size}.jpg";
      $urls[$size] = Storage::disk(self::STORAGE_DISK)->url($sizePath);
    }

    return $urls;
  }
}

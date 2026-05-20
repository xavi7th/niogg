<?php

namespace Modules\PublicPage\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\EventPhoto;
use Intervention\Image\Drivers\Imagick\Driver;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;

class EventPhotoUploadService
{
  private const DISK = 'public';

  private const PHOTO_PATH = 'event-photos';

  private const THUMB_PATH = 'event-photos/thumbnails';

  private const THUMB_WIDTH = 320;

  private const THUMB_HEIGHT = 240;

  private readonly ImageManager $imageManager;

  public function __construct()
  {
    $this->imageManager = new ImageManager(new Driver()); // phpcs:ignore Squiz.WhiteSpace.OperatorSpacing
  }

  public function store(UploadedFile $file, Event $event, int $sortOrder): EventPhoto
  {
    $uuid = (string) Str::uuid();
    $extension = mb_strtolower($file->getClientOriginalExtension());
    $photoPath = $file->storeAs(self::PHOTO_PATH, $uuid . '.' . $extension, self::DISK);

    $photo = $event->photos()->create([
      'photo_url' => Storage::disk(self::DISK)->url($photoPath),
      'thumbnail_url' => NULL,
      'sort_order' => $sortOrder,
    ]);

    GeneratePhotoThumbnail::dispatch($photo);

    return $photo;
  }

  public function generateThumbnail(EventPhoto $photo): void
  {
    $path = $this->urlToPath($photo->photo_url);
    if ( ! $path || ! Storage::disk(self::DISK)->exists($path)) {
      return;
    }

    $uuid = pathinfo($path, PATHINFO_FILENAME);
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $thumbRelative = self::THUMB_PATH . '/' . $uuid . '.' . $extension;
    $thumbAbsolute = Storage::disk(self::DISK)->path($thumbRelative);

    Storage::disk(self::DISK)->makeDirectory(self::THUMB_PATH);

    $this->imageManager
        ->read(Storage::disk(self::DISK)->path($path))
        ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT)
        ->save($thumbAbsolute);

    $photo->update([
      'thumbnail_url' => Storage::disk(self::DISK)->url($thumbRelative),
    ]);
  }

  public function delete(EventPhoto $photo): void
  {
    foreach ([$photo->photo_url, $photo->thumbnail_url] as $url) {
      $path = $this->urlToPath($url);
      if ($path && Storage::disk(self::DISK)->exists($path)) {
        Storage::disk(self::DISK)->delete($path);
      }
    }
  }

  private function urlToPath(?string $url): ?string
  {
    if (empty($url)) {
      return NULL;
    }

    return mb_ltrim(str_replace(Storage::disk(self::DISK)->url(''), '', $url), '/');
  }
}

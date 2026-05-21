# Performance Fixes — Final Implementation Plan

> All decisions resolved. Implement this file only.

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_GROUP: all complete
STATUS: done
LAST_UPDATED: 2026-05-20
```

### Parallelization

Fixes are organized into three independent groups that can run simultaneously:

| Group | Fixes | What | Dependencies |
|---|---|---|---|
| **A — SSH env changes** | 4, 5, 7 | `sed` commands on server `.env` | None |
| **B — Code: images** | 2, 6 | Switch GD→Imagick + queue jobs | Fix 6 depends on Fix 2 |
| **C — Code: caching** | 1, 3 | `deploy.sh` tweak + install spatie/laravel-responsecache | None |

Groups A, B, C have **zero dependency on each other** — all can run in parallel.

### Progress Table

| Fix | Title | Status | Date | Notes |
|-----|-------|--------|------|-------|
| 1 | Fix `config:cache` on deploy | ✅ | 2026-05-20 | `mkdir -p bootstrap/cache` added to deploy.sh |
| 2 | Switch GD → Imagick | ✅ | 2026-05-20 | Both services updated |
| 3 | HTTP page cache | ✅ | 2026-05-20 | spatie/laravel-responsecache installed + configured |
| 4 | Fix `.env` settings | ✅ | 2026-05-20 | Applied live on production |
| 5 | APCu cache driver | ✅ | 2026-05-20 | Applied live on production |
| 6 | Queue image processing | ✅ | 2026-05-20 | Jobs, retry endpoint, scheduler, admin UI |
| 7 | Cookie sessions | ✅ | 2026-05-20 | Applied live on production |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Groups A, B, C can run in parallel. Within group B, implement Fix 2 before Fix 6. After each fix, update the SESSION STATE and progress table.

### Rules — follow without exception

1. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
2. **All Artisan/Composer/Bun commands go through Sail** (`vendor/bin/sail ...`).
3. **Run `vendor/bin/sail bin pint --dirty` after each fix.**
4. **Check sibling files for conventions** before writing any new PHP file.
5. **Do not create any file not listed in this plan.**
6. **SSH env changes (Group A) run live on production. Code changes (B, C) are local, tested, then deployed.**

---

## CURRENT STATE

- Home page: 4.57s TTFB (zero DB queries — pure framework boot time)
- Image upload (300KB): 5+ minutes
- Serving from Namecheap shared hosting (48 cores shared, LSAPI, LiteSpeed)

### Root Causes

1. **`bootstrap/cache/config.php` missing** — `composer recompile` runs during deploy but `config.php` isn't on the server. The `bootstrap/cache/` directory creation happens too late.
2. **Image processing uses GD** — Both `EventPhotoUploadService` and `VideoThumbnailService` use Intervention Image with GD driver. Server has Imagick 7.1.2 installed.
3. **No HTTP page cache** — Every visitor runs full Laravel boot + middleware + Inertia rendering.
4. **`APP_ENV=local`, `APP_DEBUG=true`, `LOG_LEVEL=debug`, `TELESCOPE_ENABLED=TRUE`** — all add overhead.
5. **`CACHE_DRIVER=file`** — file I/O on contended shared storage.
6. **Image processing blocks HTTP response** — admin waits minutes for thumbnails.
7. **`SESSION_DRIVER=file`** — slow writes on shared hosting I/O.

---

## GROUP A — SSH Env Changes (parallel-safe, zero code)

### Fix 4 — Fix `.env` Settings

**Goal:** Set production-appropriate env values. All four via one SSH command.

```bash
ssh niogg-server '
  cd ~/niogg.org/shared/env
  sed -i "s/APP_ENV=local/APP_ENV=production/" .env
  sed -i "s/APP_DEBUG=true/APP_DEBUG=false/" .env
  sed -i "s/LOG_LEVEL=debug/LOG_LEVEL=warning/" .env
  sed -i "s/TELESCOPE_ENABLED=TRUE/TELESCOPE_ENABLED=FALSE/" .env
'
```

### Fix 5 — Switch Cache Driver to APCu

**Goal:** Reduce disk I/O by using shared memory cache.

```bash
ssh niogg-server "sed -i 's/CACHE_DRIVER=file/CACHE_DRIVER=apc/' ~/niogg.org/shared/env/.env"
```

**Rollback:**
```bash
ssh niogg-server "sed -i 's/CACHE_DRIVER=apc/CACHE_DRIVER=file/' ~/niogg.org/shared/env/.env"
```

### Fix 7 — Switch Session Driver to Cookie

**Goal:** Eliminate session file I/O. Encrypted client-side cookie fits typical Inertia session data (<1KB).

```bash
ssh niogg-server "sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=cookie/' ~/niogg.org/shared/env/.env"
```

---

## GROUP B — Code: Images (Fix 2 → Fix 6)

### Fix 2 — Switch GD to Imagick

**Goal:** Replace slow GD image processing with Imagick. Server has ImageMagick 7.1.2 + Imagick 3.8.0.

#### Step 2.1 — `EventPhotoUploadService.php`

Edit `Modules/PublicPage/app/Services/EventPhotoUploadService.php`:

Change line 36 from:
```php
ImageManager::gd()
```
to:
```php
new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver)
```

#### Step 2.2 — `VideoThumbnailService.php`

Edit `Modules/PublicPage/app/Services/VideoThumbnailService.php`:

Change import (line 13):
```php
use Intervention\Image\Drivers\Imagick\Driver;
```

Change constructor (line 36):
```php
$this->imageManager = new ImageManager(new Driver);
```

### Fix 6 — Queue Image Processing

**Goal:** Offload thumbnail generation to a queue job so admin uploads return instantly.

#### Step 6.1 — Create `GeneratePhotoThumbnail` job

Create `Modules/PublicPage/app/Jobs/GeneratePhotoThumbnail.php`:

```php
<?php

namespace Modules\PublicPage\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Services\EventPhotoUploadService;

class GeneratePhotoThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $tries = 3;
    private int $timeout = 30;

    public function __construct(public EventPhoto $photo) {}

    public function handle(EventPhotoUploadService $service): void
    {
        $service->generateThumbnail($this->photo);
    }

    public function failed(\Exception $exception): void
    {
        $this->photo->update(['thumbnail_generation' => 'failed']);
    }
}
```

#### Step 6.2 — Add `generateThumbnail()` to `EventPhotoUploadService`

Edit `Modules/PublicPage/app/Services/EventPhotoUploadService.php`. Add:

```php
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
```

Replace the existing `store()` method to dispatch a job:

```php
public function store(UploadedFile $file, Event $event, int $sortOrder): EventPhoto
{
    $uuid = (string) Str::uuid();
    $extension = mb_strtolower($file->getClientOriginalExtension());
    $photoPath = $file->storeAs(self::PHOTO_PATH, "{$uuid}.{$extension}", self::DISK);

    $photo = $event->photos()->create([
        'photo_url' => Storage::disk(self::DISK)->url($photoPath),
        'thumbnail_url' => null,
        'sort_order' => $sortOrder,
    ]);

    GeneratePhotoThumbnail::dispatch($photo);

    return $photo;
}
```

Add new method:

```php
public function generateThumbnail(EventPhoto $photo): void
{
    $path = $this->urlToPath($photo->photo_url);
    if (!$path || !Storage::disk(self::DISK)->exists($path)) {
        return;
    }

    $uuid = pathinfo($path, PATHINFO_FILENAME);
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $thumbRelative = self::THUMB_PATH . "/{$uuid}.{$extension}";
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
```

#### Step 6.3 — Add `retryThumbnail` endpoint to `AdminPhotoController`

Edit `Modules/PublicPage/app/Http/Controllers/Admin/AdminPhotoController.php`. Add import:

```php
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
```

Add method:

```php
public function retryThumbnail(EventPhoto $photo): JsonResponse
{
    if ($photo->thumbnail_url) {
        return response()->json(['message' => 'Thumbnail already exists.']);
    }

    GeneratePhotoThumbnail::dispatch($photo);

    return response()->json(['message' => 'Thumbnail generation queued.']);
}
```

#### Step 6.4 — Add route

Edit `Modules/PublicPage/routes/web.php`. Inside `admin` → `photos` prefix group, add:

```php
Route::post('/{photo}/retry-thumbnail', [AdminPhotoController::class, 'retryThumbnail'])
    ->name('photos.retry-thumbnail');
```

Named route: `admin.photos.retry-thumbnail`.

#### Step 6.5 — Add retry button to admin UI

Edit `Modules/PublicPage/resources/js/Components/Admin/AdminPhotosTab.svelte`.

Add a "Retry Thumbnail" button on each photo where `thumbnail_url` is null or `thumbnail_generation` is `failed`. The button calls:

```js
await fetch(`/admin/photos/${photo.id}/retry-thumbnail`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken },
});
```

#### Step 6.6 — Create scheduled retry command

```bash
vendor/bin/sail artisan make:command RetryFailedThumbnails --no-interaction
```

Edit `app/Console/Commands/RetryFailedThumbnails.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Modules\PublicPage\Models\EventPhoto;

class RetryFailedThumbnails extends Command
{
    protected $signature = 'photos:retry-failed-thumbnails';

    protected $description = 'Re-dispatch thumbnail generation for photos with missing or failed thumbnails';

    public function handle(): void
    {
        EventPhoto::query()
            ->whereNull('thumbnail_url')
            ->orWhere('thumbnail_generation', 'failed')
            ->each(fn (EventPhoto $photo) => GeneratePhotoThumbnail::dispatch($photo));
    }
}
```

Edit `app/Console/Kernel.php`. Add to `schedule()`:

```php
$schedule->command('photos:retry-failed-thumbnails')->everyThirtyMinutes();
```

#### Step 6.7 — Similarly for video thumbnails

Create `Modules/PublicPage/app/Jobs/GenerateVideoThumbnails.php` following the same pattern. Dispatch from `VideoUploadService` instead of calling `generateForVideo()` synchronously.

#### Step 6.8 — Queue worker

The production server already runs a queue worker for `telegram-queue`. Ensure it also listens to the `default` queue:

```bash
# Check current:
ssh niogg-server "ps aux | grep queue:work"

# If needed (e.g. via cron):
# php artisan queue:work --queue=default,telegram-queue --max-jobs=10 --max-time=840
```

---

## GROUP C — Code: Caching (Fix 1 + Fix 3)

### Fix 1 — Fix `config:cache` on Deploy

**Goal:** Ensure `config.php` is written to `bootstrap/cache/` during deploy.

#### Step 1.1 — Edit `deploy.sh`

Find the remote commands block where `cd "$REL"` happens (around line 160). Add **before** `composer install`:

```bash
# Ensure bootstrap/cache exists before composer runs
echo "  → Ensuring bootstrap/cache exists..."
mkdir -p bootstrap/cache
```

### Fix 3 — HTTP Page Cache

**Goal:** Cache full HTTP responses for public pages so repeat visits return in ~200ms. Use `spatie/laravel-responsecache`.

#### Step 3.1 — Install package

```bash
vendor/bin/sail composer require spatie/laravel-responsecache --no-interaction
```

#### Step 3.2 — Publish config

```bash
vendor/bin/sail artisan vendor:publish --tag=responsecache-config --no-interaction
```

#### Step 3.3 — Configure `config/responsecache.php`

Set:
```php
'cache_lifetime_in_seconds' => 86400, // 24h
```

#### Step 3.4 — Create custom cache profile

Create `app/Support/ResponseCacheProfile.php`:

```php
<?php

namespace App\Support;

use Spatie\ResponseCache\CacheProfiles\CacheAllSuccessfulGetRequests;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponseCacheProfile extends CacheAllSuccessfulGetRequests
{
    public function shouldCacheRequest(Request $request): bool
    {
        if ($request->is('admin/*') || $request->is('administrative-logs/*') || $request->is('application-logs/*')) {
            return false;
        }

        if ($request->getMethod() !== 'GET') {
            return false;
        }

        return parent::shouldCacheRequest($request);
    }

    public function shouldCacheResponse(Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        // Don't cache pages with flash messages (form submissions)
        if (session()->has('flash')) {
            return false;
        }

        return parent::shouldCacheResponse($response);
    }
}
```

Update `config/responsecache.php`:
```php
'cache_profile' => App\Support\ResponseCacheProfile::class,
```

#### Step 3.5 — Register middleware

Edit `app/Http/Kernel.php`. Add to `$middlewareGroups['web']` (after `SubstituteBindings`, before `HandleInertiaRequests`):

```php
\Spatie\ResponseCache\Middlewares\CacheResponse::class,
```

Also add `\Spatie\ResponseCache\Middlewares\DoNotCacheResponse` as a route middleware alias in `$middlewareAliases`:
```php
'doNotCacheResponse' => \Spatie\ResponseCache\Middlewares\DoNotCacheResponse::class,
```

#### Step 3.6 — Add cache invalidation to models

Edit `Modules/PublicPage/app/Models/Event.php`. Add:

```php
use Spatie\ResponseCache\Facades\ResponseCache;

protected static function booted(): void
{
    static::saved(fn () => ResponseCache::clear());
    static::deleted(fn () => ResponseCache::clear());
}
```

Same pattern for `EventPhoto` and `Video` models.

---

## Verification

| Fix | How to verify |
|---|---|
| **1** | `ssh niogg-server 'ls ~/niogg.org/current/bootstrap/cache/config.php'` — file exists |
| **2** | Upload photo — compare time before/after |
| **3** | First visit to `/`: ~4s TTFB. Second visit: ~200ms |
| **4** | `ssh niogg-server 'grep -E "APP_ENV\|APP_DEBUG\|LOG_LEVEL\|TELESCOPE" ~/niogg.org/shared/env/.env'` |
| **5** | After deploy: `ssh niogg-server "php -r 'var_dump(apcu_cache_info(false));'"` — shows entries |
| **6** | Upload photo — responds instantly. Thumbnail appears seconds later. Retry button works if job fails. |
| **7** | Log in to admin — session persists. Check cookie size (<1KB). |

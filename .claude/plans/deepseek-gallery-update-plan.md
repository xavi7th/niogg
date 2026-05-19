# Gallery Update: Event Photos + Dynamic Gallery + enhanced:img Fix

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 0
STATUS: not started
LAST_UPDATED: 2026-05-19
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | Migration + EventPhoto model | ⏳ | — | |
| 1a | Add old gallery filter labels as event categories | ⏳ | — | Form options + validation + seeder |
| 2 | EventPhotoUploadService + AdminPhotoController | ⏳ | — | |
| 3 | Admin UI: Tab switcher + AdminPhotosTab component | ⏳ | — | |
| 4 | Replace `/events/{slug}` with EventDetail page | ⏳ | — | Photos + video playlist |
| 5 | Public UI: Update Media Showcase with photo count badges | ⏳ | — | |
| 6 | Rewrite `/gallery` as dynamic photo browsing page | ⏳ | — | |
| 7 | Fix `enhanced:img` across all 14 files | ⏳ | — | |
| 8 | Tests | ⏳ | — | |
| 9 | Gallery image cleanup (manual) | ⏳ | — | |

---

## Architecture Overview

```
Events (already exists)
  ├── Videos (already exists)
  │     displayed on: /events/media-showcase, /events/{slug} (new detail page)
  └── Photos (NEW via EventPhoto model)
        displayed on: /gallery (dynamic photo grid, filterable by event category)
                     /events/{slug} (photo preview + lightbox + video playlist)

/gallery              → Rewritten: dynamic photo grid from event_photos table
/events/media-showcase→ Updated: photo count badges on event cards
/events/{slug}        → REPLACED: was video-only grid with modal, now:
                          EventHeader → description → photo gallery (capped) → video playlist
/events/{slug}/videos → Unchanged: existing AJAX pagination endpoint (backward compat)
```

### Safety: Video Conversion Schedule NOT Affected

The existing video conversion pipeline (queued jobs, artisan commands, cron schedules) is **completely untouched** by this plan:

| File/Component | Modified? | Reason |
|----------------|-----------|--------|
| `app/Console/Kernel.php` (schedule) | ❌ No | Not in scope |
| `ConvertVideoToMp4.php` (job) | ❌ No | Not in scope |
| `ConvertPendingVideos.php` (command) | ❌ No | Not in scope |
| `EventVideoUploadService.php` | ❌ No | New `EventPhotoUploadService` is a separate file |
| `AdminVideoController.php` | ❌ No | New `AdminPhotoController` is a separate file |
| `Video` model | ❌ No | `EventPhoto` model is a separate file |
| 3 video migrations | ❌ No | New migration for `event_photos` table only |

The plan only touches the `Event` model (adds `photos()` relationship) and adds entirely new files. No video job, queue, or schedule logic is altered.

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Separate table or JSON column? | ✅ **Separate `event_photos`** — normalized, filterable, sortable |
| 2 | Storage disk? | ✅ **`public` disk** at `storage/app/public/event-photos/` |
| 3 | Image processing? | ✅ **Intervention Image** — already a dependency via VideoThumbnailService |
| 4 | Thumbnail sizes? | ✅ **320×240 cover** for grid, full-size for lightbox |
| 5 | Photo fatigue guard? | ✅ **12-photo preview cap** with "View all X photos" button |
| 6 | Admin UI pattern? | ✅ **Tab switcher** (Videos / Photos) on event show page |
| 7 | Alt text editing? | ✅ **Inline on-blur PUT** on `alt_text` field |
| 8 | Photo reorder? | ✅ **Drag-drop reorder endpoint** |
| 9 | Gallery URL? | ✅ **Keep `/gallery`** — rewrite as dynamic photo-first browsing page, distinct from event-card-focused media showcase |
| 10 | `enhanced:img` fix? | ✅ **Phase 7** replaces all instances across 14 files |
| 11 | Video playlist? | ✅ **Side-by-side** player + up-next with auto-advance on event detail page |
| 12 | Cascade delete? | ✅ FK `cascadeOnDelete` on event_photos |
| 13 | Dynamic images and enhanced-img? | ✅ `@sveltejs/enhanced-img` works at build time, not for uploaded images. Uploaded photos use plain `<img>` with storage URLs. Static template images switch from `enhanced:img` to `getImgUrl()` pattern. |
| 14 | `/events/{slug}` vs new `/detail` route? | ✅ **Replace `/events/{slug}` in-place** — no separate detail route. Video-only grid becomes EventDetail with photo gallery + video playlist. |
| 15 | Video retry logic? | ✅ **Preserved from EventVideosGrid.svelte** — `MAX_RETRIES`, `handleVideoError`, `handleRetry` carried into new EventDetail page |

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 1a → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9). Never skip ahead.
2. **Use the exact code provided.** Copy verbatim. Only adapt the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **Run every bash command exactly as written.**
5. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
6. **Run `vendor/bin/sail bin pint` after each phase** to match project code style.

---

## PHASE 1 — Migration + EventPhoto model

**Goal:** Create the database table and Eloquent model.

### Step 1.1 — Create migration

```bash
vendor/bin/sail artisan make:migration create_event_photos_table --path=Modules/PublicPage/database/migrations --no-interaction
```

Edit the generated migration with `photo_url`, `thumbnail_url`, `alt_text`, and `sort_order`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_photos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('photo_url');
            $table->string('thumbnail_url');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
    }
};
```

### Step 1.2 — Create EventPhoto model

```bash
vendor/bin/sail artisan make:model EventPhoto --path=Modules/PublicPage/app/Models --no-interaction
```

Edit `Modules/PublicPage/app/Models/EventPhoto.php`:

```php
<?php

namespace Modules\PublicPage\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\PublicPage\Database\Factories\EventPhotoFactory;

class EventPhoto extends Model
{
    use HasFactory;

    protected $table = 'event_photos';

    protected $fillable = [
        'event_id',
        'photo_url',
        'thumbnail_url',
        'alt_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function newFactory(): EventPhotoFactory
    {
        return EventPhotoFactory::new();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
```

### Step 1.3 — Add `photos` relationship to Event model

Edit `Modules/PublicPage/app/Models/Event.php`. Add the import:

```php
use Modules\PublicPage\Models\EventPhoto;
```

Add after `videos()`:

```php
public function photos(): HasMany
{
    return $this->hasMany(EventPhoto::class)->ordered();
}
```

Add the `HasMany` import if not already present:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```

### Step 1.4 — Run migration

```bash
vendor/bin/sail artisan migrate --no-interaction
```

### Step 1.5 — Create EventPhoto factory

Create `Modules/PublicPage/database/factories/EventPhotoFactory.php`:

```php
<?php

namespace Modules\PublicPage\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;

class EventPhotoFactory extends Factory
{
    protected $model = EventPhoto::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'photo_url' => '/storage/event-photos/' . $this->faker->uuid . '.jpg',
            'thumbnail_url' => '/storage/event-photos/thumbnails/' . $this->faker->uuid . '.jpg',
            'alt_text' => $this->faker->optional()->sentence(4),
            'sort_order' => 0,
        ];
    }
}
```

---

## PHASE 1a — Add old gallery filter labels as event categories

**Goal:** Add the old static gallery filter labels (Protests, Awards, Charity Drive, Elections, Free Medicals) as official event categories so admins can assign them when creating events. This makes them appear as gallery filter options without hardcoding.

### Step 1a.1 — Create database seeder for categories

Create `Modules/PublicPage/database/seeders/EventCategorySeeder.php`:

```php
<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    public const CATEGORIES = [
        'Conference',
        'Workshop',
        'Entertainment',
        'Sports',
        'Education',
        'Other',
        'Protests',
        'Awards',
        'Charity Drive',
        'Elections',
        'Free Medicals',
    ];

    public function run(): void
    {
        $this->command->info('Event categories available: ' . implode(', ', self::CATEGORIES));
    }
}
```

### Step 1a.2 — Register seeder in DatabaseSeeder

Edit `Modules/PublicPage/database/seeders/DatabaseSeeder.php` (create if it doesn't exist):

```php
<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EventCategorySeeder::class,
        ]);
    }
}
```

### Step 1a.3 — Run seeder

```bash
vendor/bin/sail artisan db:seed --class="Modules\\PublicPage\\Database\\Seeders\\EventCategorySeeder"
```

### Step 1a.4 — Update EventFormRequest validation rules

Edit `Modules/PublicPage/app/Http/Requests/Admin/EventFormRequest.php`. Add imports:

```php
use Modules\PublicPage\Database\Seeders\EventCategorySeeder;
use Illuminate\Validation\Rule;
```

Update the `category` rule:

```php
'category' => ['required', 'string', 'max:255', Rule::in(EventCategorySeeder::CATEGORIES)],
```

### Step 1a.5 — Update admin event form category options

Edit both `Modules/PublicPage/resources/js/Pages/Admin/Events/Create.svelte` and `Edit.svelte`. Replace the hardcoded `<option>` elements in the category select with:

```svelte
<option value="">Select category</option>
<option>Conference</option>
<option>Workshop</option>
<option>Entertainment</option>
<option>Sports</option>
<option>Education</option>
<option>Other</option>
<option>Protests</option>
<option>Awards</option>
<option>Charity Drive</option>
<option>Elections</option>
<option>Free Medicals</option>
```

This ensures that when an admin creates an event with category "Protests" or "Awards", the gallery's category filter automatically picks it up without any extra configuration.

---

## PHASE 2 — EventPhotoUploadService + AdminPhotoController

**Goal:** Service for uploading/deleting photos, controller with CRUD endpoints.

### Step 2.1 — Create EventPhotoUploadService

Create `Modules/PublicPage/app/Services/EventPhotoUploadService.php`:

```php
<?php

namespace Modules\PublicPage\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;

class EventPhotoUploadService
{
    private const DISK = 'public';
    private const PHOTO_PATH = 'event-photos';
    private const THUMB_PATH = 'event-photos/thumbnails';
    private const THUMB_WIDTH = 320;
    private const THUMB_HEIGHT = 240;

    public function store(UploadedFile $file, Event $event, int $sortOrder): EventPhoto
    {
        $uuid = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());

        $photoPath = $file->storeAs(self::PHOTO_PATH, "{$uuid}.{$extension}", self::DISK);

        $thumbRelative = self::THUMB_PATH . "/{$uuid}.{$extension}";
        $thumbAbsolute = Storage::disk(self::DISK)->path($thumbRelative);

        Storage::disk(self::DISK)->makeDirectory(self::THUMB_PATH);

        ImageManager::gd()
            ->read(Storage::disk(self::DISK)->path($photoPath))
            ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT)
            ->save($thumbAbsolute);

        return $event->photos()->create([
            'photo_url' => Storage::disk(self::DISK)->url($photoPath),
            'thumbnail_url' => Storage::disk(self::DISK)->url($thumbRelative),
            'sort_order' => $sortOrder,
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
            return null;
        }

        return ltrim(str_replace(Storage::disk(self::DISK)->url(''), '', $url), '/');
    }
}
```

### Step 2.2 — Create FormRequests

```bash
vendor/bin/sail artisan make:request Admin/StoreEventPhotosRequest --no-interaction
```

Edit `Modules/PublicPage/app/Http/Requests/Admin/StoreEventPhotosRequest.php`:

```php
<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin() ?? false);
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:20'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.max' => 'You can upload up to 20 photos at once.',
            'photos.*.max' => 'Each photo must be 10MB or smaller.',
        ];
    }
}
```

```bash
vendor/bin/sail artisan make:request Admin/UpdateEventPhotoRequest --no-interaction
```

Edit `Modules/PublicPage/app/Http/Requests/Admin/UpdateEventPhotoRequest.php`:

```php
<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin() ?? false);
    }

    public function rules(): array
    {
        return [
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

### Step 2.3 — Create AdminPhotoController

```bash
vendor/bin/sail artisan make:controller Admin/AdminPhotoController --no-interaction
```

Edit `Modules/PublicPage/app/Http/Controllers/Admin/AdminPhotoController.php`:

```php
<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PublicPage\Http\Requests\Admin\StoreEventPhotosRequest;
use Modules\PublicPage\Http\Requests\Admin\UpdateEventPhotoRequest;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Services\EventPhotoUploadService;

class AdminPhotoController extends Controller
{
    public function store(
        StoreEventPhotosRequest $request,
        Event $event,
        EventPhotoUploadService $photoService,
    ): JsonResponse {
        $startOrder = $event->photos()->max('sort_order') + 1;
        $photos = [];

        foreach ($request->file('photos', []) as $index => $file) {
            $photos[] = $photoService->store($file, $event, $startOrder + $index);
        }

        return response()->json([
            'message' => count($photos) . ' photo(s) uploaded successfully.',
            'photos' => $photos,
        ]);
    }

    public function update(UpdateEventPhotoRequest $request, EventPhoto $photo): JsonResponse
    {
        $photo->update($request->validated());

        return response()->json([
            'message' => 'Photo updated.',
            'photo' => $photo->fresh(),
        ]);
    }

    public function reorder(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'photo_ids' => ['required', 'array'],
            'photo_ids.*' => ['integer', 'exists:event_photos,id'],
        ]);

        foreach ($request->photo_ids as $index => $photoId) {
            EventPhoto::where('id', $photoId)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Photos reordered.']);
    }

    public function destroy(EventPhoto $photo, EventPhotoUploadService $photoService): JsonResponse
    {
        $photoService->delete($photo);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted.']);
    }
}
```

### Step 2.4 — Add admin routes

Edit `Modules/PublicPage/routes/web.php`. Add the import:

```php
use Modules\PublicPage\Http\Controllers\Admin\AdminPhotoController;
```

Inside the `admin` middleware group, add after the videos section:

```php
Route::prefix('events')->name('events.')->group(function (): void {
    Route::post('/{event}/photos', [AdminPhotoController::class, 'store'])->name('photos.store');
    Route::post('/{event}/photos/reorder', [AdminPhotoController::class, 'reorder'])->name('photos.reorder');
});

Route::prefix('photos')->name('photos.')->group(function (): void {
    Route::put('/{photo}', [AdminPhotoController::class, 'update'])->name('update');
    Route::delete('/{photo}', [AdminPhotoController::class, 'destroy'])->name('destroy');
});
```

Named routes produced:
- `admin.events.photos.store`
- `admin.events.photos.reorder`
- `admin.photos.update`
- `admin.photos.destroy`

---

## PHASE 3 — Admin UI: Tab switcher + AdminPhotosTab component

**Goal:** Add a tab switcher (Videos / Photos) to the admin event show page with full photo management extracted into a separate component.

### Step 3.1 — Create AdminPhotosTab component

Create `Modules/PublicPage/resources/js/Components/Admin/AdminPhotosTab.svelte`:

```svelte
<script>
  import { router } from '@inertiajs/svelte';

  export let event = null;

  let photoDragId = null;
  let photoDragOverId = null;
  let isPhotoReordering = false;
  let showPhotoUpload = false;
  let photoFileInput;
  let newPhotoFiles = [];
  let newPhotoPreviews = [];
  let isUploading = false;
  let showPhotoDeleteDialog = false;
  let photoToDelete = null;

  $: sortedPhotos = event?.photos
    ? [...event.photos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];

  function handlePhotoDragStart(photoId, event) {
    photoDragId = photoId;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', photoId.toString());
    event.target.closest('.group').style.opacity = '0.5';
  }

  function handlePhotoDragEnd(event) {
    photoDragId = null;
    photoDragOverId = null;
    if (event.target.closest) {
      event.target.closest('.group').style.opacity = '1';
    }
  }

  function handlePhotoDragOver(photoId, event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    if (photoDragId !== photoId) {
      photoDragOverId = photoId;
    }
  }

  function handlePhotoDragLeave(photoId) {
    if (photoDragOverId === photoId) {
      photoDragOverId = null;
    }
  }

  async function handlePhotoDrop(photoId, event) {
    event.preventDefault();
    photoDragOverId = null;

    if (photoDragId === null || photoDragId === photoId) return;

    const draggedIndex = sortedPhotos.findIndex((p) => p.id === photoDragId);
    const dropIndex = sortedPhotos.findIndex((p) => p.id === photoId);

    if (draggedIndex === -1 || dropIndex === -1) return;

    const newOrder = [...sortedPhotos];
    const [removed] = newOrder.splice(draggedIndex, 1);
    newOrder.splice(dropIndex, 0, removed);

    const photoIds = newOrder.map((p) => p.id);

    isPhotoReordering = true;
    try {
      const response = await fetch(`/admin/events/${event.id}/photos/reorder`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ photo_ids: photoIds }),
      });

      if (response.ok) {
        router.reload({ only: ['event'] });
      }
    } catch (err) {
      console.error('Reorder failed', err);
    } finally {
      isPhotoReordering = false;
      photoDragId = null;
    }
  }

  function handlePhotoSelect(event) {
    addPhotoFiles(event.target.files);
    event.target.value = '';
  }

  function handlePhotoDropEvent(event) {
    addPhotoFiles(event.dataTransfer.files);
  }

  function addPhotoFiles(files) {
    const remaining = 20 - (event?.photos?.length || 0) - newPhotoFiles.length;
    Array.from(files).slice(0, remaining).forEach((file) => {
      newPhotoFiles = [...newPhotoFiles, file];
      newPhotoPreviews = [...newPhotoPreviews, URL.createObjectURL(file)];
    });
  }

  function removeNewPhoto(index) {
    URL.revokeObjectURL(newPhotoPreviews[index]);
    newPhotoFiles = newPhotoFiles.filter((_, i) => i !== index);
    newPhotoPreviews = newPhotoPreviews.filter((_, i) => i !== index);
  }

  async function uploadPhotos() {
    if (newPhotoFiles.length === 0) return;
    isUploading = true;

    const form = new FormData();
    newPhotoFiles.forEach((file, i) => form.append(`photos[${i}]`, file));

    try {
      const response = await fetch(`/admin/events/${event.id}/photos`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: form,
      });

      if (response.ok) {
        closePhotoUpload();
        router.reload({ only: ['event'] });
        window.ToastLarge.fire({ title: 'Success', html: 'Photos uploaded.', icon: 'success', timer: 3000 });
      } else {
        const data = await response.json();
        window.ToastLarge.fire({ title: 'Error', html: data.message || 'Upload failed.', icon: 'error', timer: 5000 });
      }
    } catch (err) {
      window.ToastLarge.fire({ title: 'Error', html: 'Network error.', icon: 'error', timer: 5000 });
    } finally {
      isUploading = false;
    }
  }

  function closePhotoUpload() {
    showPhotoUpload = false;
    newPhotoFiles.forEach((_, i) => URL.revokeObjectURL(newPhotoPreviews[i]));
    newPhotoFiles = [];
    newPhotoPreviews = [];
  }

  async function updatePhotoAltText(photoId, altText) {
    try {
      await fetch(`/admin/photos/${photoId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ alt_text: altText }),
      });
    } catch (err) {
      console.error('Alt text update failed', err);
    }
  }

  function openPhotoDeleteDialog(photo) {
    photoToDelete = photo;
    showPhotoDeleteDialog = true;
  }

  function closePhotoDeleteDialog() {
    showPhotoDeleteDialog = false;
    photoToDelete = null;
  }

  async function confirmDeletePhoto() {
    if (!photoToDelete) return;

    try {
      const response = await fetch(`/admin/photos/${photoToDelete.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
      });

      if (response.ok) {
        showPhotoDeleteDialog = false;
        photoToDelete = null;
        router.reload({ only: ['event'] });
        window.ToastLarge.fire({ title: 'Success', html: 'Photo deleted.', icon: 'success', timer: 3000 });
      }
    } catch (err) {
      window.ToastLarge.fire({ title: 'Error', html: 'Network error.', icon: 'error', timer: 5000 });
    }
  }
</script>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
  <div>
    <h2 class="text-lg font-semibold text-[#1b1a1a]">
      Photos ({sortedPhotos.length})
    </h2>
    {#if sortedPhotos.length > 1}
      <p class="text-sm text-[#9b9b9b] mt-1">Drag photos to reorder them</p>
    {/if}
  </div>
  <button
    on:click={() => showPhotoUpload = true}
    class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm flex items-center gap-2"
    type="button"
  >
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
    </svg>
    Add Photos
  </button>
</div>

{#if sortedPhotos.length > 0}
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
    {#each sortedPhotos as photo}
      <div
        draggable="true"
        on:dragstart={(e) => handlePhotoDragStart(photo.id, e)}
        on:dragend={handlePhotoDragEnd}
        on:dragover={(e) => handlePhotoDragOver(photo.id, e)}
        on:dragleave={() => handlePhotoDragLeave(photo.id)}
        on:drop={(e) => handlePhotoDrop(photo.id, e)}
        class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-[#eaeaea] cursor-grab active:cursor-grabbing"
        class:ring-2={photoDragOverId === photo.id}
        class:ring-[#ff7607]={photoDragOverId === photo.id}
      >
        <img src={photo.thumbnail_url} alt={photo.alt_text || ''} class="w-full h-full object-cover" loading="lazy" />

        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
          <button
            on:click={() => openPhotoDeleteDialog(photo)}
            type="button"
            class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
          <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow">
            <svg class="w-4 h-4 text-[#9b9b9b]" fill="currentColor" viewBox="0 0 24 24">
              <path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm8-14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
            </svg>
          </div>
        </div>

        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent p-2 pt-6 opacity-0 group-hover:opacity-100 transition-opacity">
          <input
            type="text"
            value={photo.alt_text || ''}
            placeholder="Add alt text..."
            on:blur={(e) => updatePhotoAltText(photo.id, e.target.value)}
            class="w-full px-2 py-1 text-xs text-white bg-white/20 backdrop-blur-sm rounded border border-white/30 placeholder-white/50 focus:outline-none focus:ring-1 focus:ring-white"
          />
        </div>
      </div>
    {/each}
  </div>
{:else}
  <div class="bg-white rounded-lg border border-[#eaeaea] p-12 text-center">
    <svg class="w-16 h-16 text-[#9b9b9b] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    <h3 class="text-lg font-medium text-[#1b1a1a] mb-2">No photos yet</h3>
    <p class="text-[#9b9b9b] mb-4">Add photos to this event to make them appear in the public gallery.</p>
    <button
      on:click={() => showPhotoUpload = true}
      class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
      type="button"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      Add First Photo
    </button>
  </div>
{/if}

<!-- Photo Upload Modal -->
{#if showPhotoUpload}
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6" on:click|stopPropagation>
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-[#1b1a1a]">Upload Photos</h3>
        <button on:click={closePhotoUpload} type="button" class="text-[#9b9b9b] hover:text-[#1b1a1a]">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div
        class="border-2 border-dashed border-[#eaeaea] rounded-xl p-8 text-center hover:border-[#ff7607] transition-colors cursor-pointer"
        on:click={() => photoFileInput?.click()}
        on:dragover|preventDefault
        on:drop|preventDefault={handlePhotoDropEvent}
      >
        <input
          type="file"
          bind:this={photoFileInput}
          accept="image/jpeg,image/png,image/gif,image/webp"
          multiple
          class="hidden"
          on:change={handlePhotoSelect}
        />

        {#if newPhotoPreviews.length > 0}
          <div class="grid grid-cols-4 gap-2 mb-4">
            {#each newPhotoPreviews as preview, i}
              <div class="relative aspect-square rounded overflow-hidden bg-gray-100">
                <img src={preview} alt="" class="w-full h-full object-cover" />
                <button on:click={() => removeNewPhoto(i)} type="button" class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            {/each}
          </div>
        {/if}

        <p class="text-[#9b9b9b]">Click or drag photos here</p>
        <p class="text-xs text-[#9b9b9b] mt-1">JPEG, PNG, GIF, WebP. Max 10MB each. Up to 20 at once.</p>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button on:click={closePhotoUpload} type="button" class="px-4 py-2 border border-[#eaeaea] rounded-lg text-[#1b1a1a] hover:bg-[#f9f9f9] font-medium text-sm">
          Cancel
        </button>
        <button
          on:click={uploadPhotos}
          disabled={isUploading || newPhotoFiles.length === 0}
          type="button"
          class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm disabled:opacity-50"
        >
          {isUploading ? 'Uploading...' : `Upload ${newPhotoFiles.length} photo${newPhotoFiles.length !== 1 ? 's' : ''}`}
        </button>
      </div>
    </div>
  </div>
{/if}

<!-- Photo Delete Confirmation -->
{#if showPhotoDeleteDialog}
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
      <h3 class="text-lg font-semibold text-[#1b1a1a] mb-2">Delete Photo</h3>
      <p class="text-[#9b9b9b] mb-2">Are you sure you want to delete this photo?</p>
      {#if photoToDelete?.alt_text}
        <p class="text-sm text-[#1b1a1a] mb-4 italic">"{photoToDelete.alt_text}"</p>
      {/if}
      <p class="text-xs text-red-500 mb-4">This action cannot be undone. The photo file will be permanently deleted from storage.</p>
      <div class="flex justify-end gap-3">
        <button on:click={closePhotoDeleteDialog} type="button" class="px-4 py-2 border border-[#eaeaea] rounded-lg text-[#1b1a1a] hover:bg-[#f9f9f9] font-medium text-sm">
          Cancel
        </button>
        <button on:click={confirmDeletePhoto} type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium text-sm">
          Delete
        </button>
      </div>
    </div>
  </div>
{/if}
```

### Step 3.2 — Update Admin Event Show page with tab switcher

Edit `Modules/PublicPage/resources/js/Pages/Admin/Events/Show.svelte`.

**Add tab state:**
```js
let activeTab = 'videos';
```

**Import AdminPhotosTab:**
```js
import AdminPhotosTab from '@publicpage-components/Admin/AdminPhotosTab.svelte';
```

**Add tab switcher** replacing the existing Videos title. Replace the `<h2 class="text-lg font-semibold text-[#1b1a1a]">Videos ({sortedVideos.length})</h2>` heading and the section below it:

```svelte
<!-- Tab Switcher -->
<div class="border-b border-[#eaeaea] mb-6">
  <nav class="flex gap-6">
    <button
      class="py-3 border-b-2 font-medium text-sm transition-colors"
      class:border-[#ff7607]={activeTab === 'videos'}
      class:border-transparent={activeTab !== 'videos'}
      class:text-[#ff7607]={activeTab === 'videos'}
      class:text-[#9b9b9b]={activeTab !== 'videos'}
      on:click={() => activeTab = 'videos'}
      type="button"
    >
      Videos ({sortedVideos.length})
    </button>
    <button
      class="py-3 border-b-2 font-medium text-sm transition-colors"
      class:border-[#ff7607]={activeTab === 'photos'}
      class:border-transparent={activeTab !== 'photos'}
      class:text-[#ff7607]={activeTab === 'photos'}
      class:text-[#9b9b9b]={activeTab !== 'photos'}
      on:click={() => activeTab = 'photos'}
      type="button"
    >
      Photos ({event?.photos?.length || 0})
    </button>
  </nav>
</div>

<!-- Videos Tab -->
{#if activeTab === 'videos'}
  ... existing video management content (header + grid + empty state) ...
{/if}

<!-- Photos Tab -->
{#if activeTab === 'photos'}
  <AdminPhotosTab {event} />
{/if}
```

The existing video management content should be wrapped in the `{#if activeTab === 'videos'}` block exactly as-is.

### Step 3.3 — Load photos relation on event show

Edit `AdminEventController@show` to eager load photos:

```php
$event->load([
    'videos' => function ($query): void {
        $query->orderBy('sort_order')->orderBy('created_at');
    },
    'photos' => function ($query): void {
        $query->ordered();
    },
]);
```

---

## PHASE 4 — Replace `/events/{slug}` with EventDetail page

**Goal:** Replace the video-only EventVideosGrid with EventDetail — a full event page showing header, description, photo gallery (with fatigue guard), and video playlist (side-by-side player + up-next). This replaces the route in-place, no new `/detail` route.

### Step 4.1 — Update `EventsMediaShowcaseController@show`

Edit `Modules/PublicPage/app/Http/Controllers/EventsMediaShowcaseController.php`. Replace the `show()` method:

```php
public function show(Event $event)
{
    $event->load([
        'photos' => function ($query): void {
            $query->ordered();
        },
        'videos' => function ($query): void {
            $query->get();
        },
    ]);

    return Inertia::render('PublicPage::EventDetail', [
        'event' => $event,
        'pageTitle' => $event->name,
    ]);
}
```

Note: Load ALL videos (no pagination) since the playlist needs the full list for up-next navigation. The `eventVideos()` method for AJAX pagination is kept for backward compatibility.

### Step 4.2 — Rename EventVideosGrid.svelte → EventDetail.svelte

```bash
mv Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte \
   Modules/PublicPage/resources/js/Pages/EventDetail.svelte
```

### Step 4.3 — Rewrite EventDetail.svelte

Replace the content of `Modules/PublicPage/resources/js/Pages/EventDetail.svelte` with the full EventDetail page combining photo gallery and video playlist.

The page must:
1. Use the existing `PublicPageLayout`, `PageTitle`, `EventHeader` components
2. Show event description if present
3. Render photo gallery with 12-photo fatigue guard + lightbox
4. Render video playlist with side-by-side player + up-next
5. **Preserve the existing video retry logic from EventVideosGrid.svelte** (`MAX_RETRIES`, `handleVideoError`, `handleRetry`)
6. Show empty state when neither photos nor videos exist

Two new sub-components are extracted to keep the page manageable and reusable.

### Step 4.4 — Create EventPhotoGallery component

Create `Modules/PublicPage/resources/js/Components/EventPhotoGallery.svelte`:

```svelte
<script>
  export let photos = [];

  let lightboxPhotoIndex = null;
  let showAllPhotos = false;

  $: previewPhotos = showAllPhotos ? photos : photos.slice(0, 12);
  $: remainingCount = photos.length - 12;

  function openLightbox(index) {
    lightboxPhotoIndex = index;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightboxPhotoIndex = null;
    document.body.style.overflow = '';
  }

  function prevPhoto() {
    if (lightboxPhotoIndex > 0) lightboxPhotoIndex--;
  }

  function nextPhoto() {
    if (lightboxPhotoIndex < photos.length - 1) lightboxPhotoIndex++;
  }

  function handleKeydown(event) {
    if (lightboxPhotoIndex !== null) {
      if (event.key === 'Escape') closeLightbox();
      if (event.key === 'ArrowLeft') prevPhoto();
      if (event.key === 'ArrowRight') nextPhoto();
    }
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Photo Gallery</h2>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
  {#each previewPhotos as photo, i}
    <button
      on:click={() => openLightbox(i)}
      class="aspect-square rounded-xl overflow-hidden hover:ring-2 hover:ring-[#ff7607] transition-all cursor-pointer bg-gray-100"
      type="button"
    >
      <img
        src={photo.thumbnail_url}
        alt={photo.alt_text || ''}
        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />
    </button>
  {/each}
</div>

{#if !showAllPhotos && photos.length > 12}
  <div class="text-center mt-6">
    <button
      on:click={() => showAllPhotos = true}
      class="px-6 py-3 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium transition-colors"
      type="button"
    >
      View all {photos.length} photos
    </button>
  </div>
{/if}

<!-- Lightbox -->
{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photos.length}
      </p>

      <img
        src={photos[lightboxPhotoIndex].photo_url}
        alt={photos[lightboxPhotoIndex].alt_text || ''}
        class="max-h-[80vh] mx-auto object-contain rounded-lg"
      />

      {#if photos[lightboxPhotoIndex].alt_text}
        <p class="text-white text-center mt-4 text-sm opacity-80">{photos[lightboxPhotoIndex].alt_text}</p>
      {/if}

      {#if lightboxPhotoIndex > 0}
        <button on:click={prevPhoto} class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      {/if}
      {#if lightboxPhotoIndex < photos.length - 1}
        <button on:click={nextPhoto} class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      {/if}
    </div>
  </div>
{/if}
```

### Step 4.5 — Create EventVideoPlaylist component

Create `Modules/PublicPage/resources/js/Components/EventVideoPlaylist.svelte`:

```svelte
<script>
  export let videos = [];

  let activeVideoId = null;
  let videoError = null;
  let retryCount = 0;
  const MAX_RETRIES = 3;
  let activeVideoElement = null;

  $: activeVideo = videos.find((v) => v.id === activeVideoId) || videos[0] || null;
  $: activeVideoIndex = videos.findIndex((v) => v.id === activeVideoId);

  $: if (videos.length > 0 && !activeVideoId) {
    activeVideoId = videos[0].id;
  }

  // Reset retry state when video changes
  $: if (activeVideoId) {
    videoError = null;
    retryCount = 0;
  }

  function setActiveVideo(video) {
    activeVideoId = video.id;
    videoError = null;
    retryCount = 0;
  }

  function nextVideo() {
    if (activeVideoIndex < videos.length - 1) {
      activeVideoId = videos[activeVideoIndex + 1].id;
    }
  }

  function prevVideo() {
    if (activeVideoIndex > 0) {
      activeVideoId = videos[activeVideoIndex - 1].id;
    }
  }

  const handleVideoError = (event) => {
    const video = event.target;
    const errorCode = video.error ? video.error.code : null;

    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = {
        message: 'There was an error loading this video.',
        retryable: retryCount < MAX_RETRIES
      };
    } else {
      videoError = {
        message: getVideoErrorMessage(errorCode),
        retryable: false
      };
    }
  };

  const getVideoErrorMessage = (code) => {
    switch (code) {
      case 2:
        return 'Network error occurred while loading the video. Please check your internet connection.';
      case 3:
        return 'The video could not be decoded. The file may be corrupted or in an unsupported format.';
      default:
        return 'An unexpected error occurred while loading the video.';
    }
  };

  const handleRetry = () => {
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = null;
    }
  };

  function formatDuration(seconds) {
    if (!seconds) return '';
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  }
</script>

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Videos</h2>

<div class="flex flex-col lg:flex-row gap-6">
  <!-- Player -->
  <div class="flex-1">
    {#if activeVideo}
      <div class="bg-black rounded-xl overflow-hidden">
        {#if videoError}
          <div class="aspect-video flex flex-col items-center justify-center bg-gray-900 text-white p-6">
            <p class="text-red-400 mb-4">{videoError.message}</p>
            {#if videoError.retryable && retryCount < MAX_RETRIES}
              <button on:click={handleRetry} class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm">
                Retry ({MAX_RETRIES - retryCount} attempts remaining)
              </button>
            {/if}
          </div>
        {:else}
          <video
            controls
            class="w-full aspect-video"
            poster={activeVideo.thumbnail_url}
            on:ended={nextVideo}
            on:error={handleVideoError}
            bind:this={activeVideoElement}
          >
            <source src={activeVideo.video_url} type={activeVideo.mime_type || 'video/mp4'} />
          </video>
        {/if}
      </div>
      <div class="mt-4">
        <h3 class="text-xl font-semibold text-[#1b1a1a]">{activeVideo.title}</h3>
        {#if activeVideo.description}
          <p class="text-[#9b9b9b] mt-2 line-clamp-3">{activeVideo.description}</p>
        {/if}
      </div>
    {:else}
      <div class="bg-black rounded-xl aspect-video flex items-center justify-center">
        <p class="text-white opacity-50">No video selected</p>
      </div>
    {/if}
  </div>

  <!-- Up Next Playlist -->
  <div class="w-full lg:w-80 xl:w-96">
    <h4 class="font-medium text-[#1b1a1a] mb-3">Up Next</h4>
    <div class="space-y-2 max-h-[600px] overflow-y-auto">
      {#each videos as video, i}
        <button
          on:click={() => setActiveVideo(video)}
          class="w-full flex gap-3 p-2 rounded-lg transition-colors text-left"
          class:bg-[#ff7607]/10={activeVideo?.id === video.id}
          class:bg-white={activeVideo?.id !== video.id}
          class:border-l-4={activeVideo?.id === video.id}
          class:border-[#ff7607]={activeVideo?.id === video.id}
          class:border-transparent={activeVideo?.id !== video.id}
          class:hover:bg-gray-50={activeVideo?.id !== video.id}
          type="button"
        >
          <div class="w-24 h-16 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
            {#if video.thumbnail_url}
              <img src={video.thumbnail_url} alt={video.title} class="w-full h-full object-cover" loading="lazy" />
            {:else}
              <div class="w-full h-full flex items-center justify-center text-[#9b9b9b]">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M8 5v14l11-7z" />
                </svg>
              </div>
            {/if}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate" class:text-[#1b1a1a]={activeVideo?.id !== video.id} class:text-[#ff7607]={activeVideo?.id === video.id}>
              {i + 1}. {video.title}
            </p>
            {#if video.duration_seconds}
              <p class="text-xs text-[#9b9b9b] mt-1">{formatDuration(video.duration_seconds)}</p>
            {/if}
          </div>
        </button>
      {/each}
    </div>
  </div>
</div>
```

### Step 4.6 — Write EventDetail.svelte

The renamed file now imports and composes the two new components:

```svelte
<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import EventHeader from '@publicpage-components/EventHeader.svelte';
  import EventPhotoGallery from '@publicpage-components/EventPhotoGallery.svelte';
  import EventVideoPlaylist from '@publicpage-components/EventVideoPlaylist.svelte';

  $: ({ event, app } = $page.props);

  $: sortedPhotos = event?.photos
    ? [...event.photos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];

  $: sortedVideos = event?.videos
    ? [...event.videos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];
</script>

<PageTitle appName={app.name} pageTitle={event?.name || 'Event Detail'}>
  <li class="breadcrumb-item"><a href={route('events.media-showcase')}>Events Media Showcase</a></li>
  <li class="breadcrumb-item active" aria-current="page">{event?.name}</li>
</PageTitle>

<EventHeader {event} />

<section class="py-12 bg-gray-50">
  <div class="container">
    {#if event?.description}
      <div class="mb-10 max-w-3xl">
        <p class="text-lg text-[#555] leading-relaxed">{event.description}</p>
      </div>
    {/if}

    {#if sortedPhotos.length > 0}
      <div class="mb-12">
        <EventPhotoGallery photos={sortedPhotos} />
      </div>
    {/if}

    {#if sortedVideos.length > 0}
      <div class="mb-12">
        <EventVideoPlaylist videos={sortedVideos} />
      </div>
    {/if}

    {#if sortedPhotos.length === 0 && sortedVideos.length === 0}
      <div class="text-center py-16">
        <p class="text-[#9b9b9b]">No media available for this event yet.</p>
        <a href={route('events.media-showcase')} class="text-[#ff7607] hover:underline mt-2 inline-block">Back to events</a>
      </div>
    {/if}
  </div>
</section>
```

---

## PHASE 5 — Update Media Showcase with photo count badges

**Goal:** Show photo + video counts per event on the media showcase index.

### Step 5.1 — Add `withCount` to controller

Edit `EventsMediaShowcaseController@index`:

```php
$events = Event::withCount(['videos', 'photos'])
    ->published()
    ->ordered($direction)
    ->get();
```

Replace `Event::with('videos')` with the above. The existing `videos` relation data is no longer needed on the index (the showcase only shows counts). If any template code relies on `event.videos`, verify and adjust.

### Step 5.2 — Add photo + video count badge to event cards

Edit `Modules/PublicPage/resources/js/Pages/EventsMediaShowcase.svelte`. Find where event cards are rendered (likely in an `EventTimeline` or child component). Add the count badge:

```svelte
<span class="flex items-center gap-3 text-sm text-[#9b9b9b]">
  {#if event.photos_count > 0}
    <span class="flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      {event.photos_count}
    </span>
  {/if}
  {#if event.videos_count > 0}
    <span class="flex items-center gap-1">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M8 5v14l11-7z" />
      </svg>
      {event.videos_count}
    </span>
  {/if}
</span>
```

---

## PHASE 6 — Rewrite `/gallery` as dynamic photo browsing page

**Goal:** Replace the static 6-image gallery with a dynamic photo grid from the database, filterable by event category, with lightbox + pagination.

### Step 6.1 — Update gallery controller

Replace the `gallery()` method in `PublicPageController.php`:

```php
public function gallery()
{
    $category = request('category');

    $photos = \Modules\PublicPage\Models\EventPhoto::query()
        ->whereHas('event', function ($query): void {
            $query->published();
        })
        ->with('event:id,name,slug,category')
        ->when($category, function ($query, $category): void {
            $query->whereHas('event', function ($q) use ($category): void {
                $q->where('category', $category);
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(24);

    $categories = \Modules\PublicPage\Models\Event::published()
        ->whereHas('photos')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return Inertia::render('PublicPage::Gallery', [
        'pageTitle' => 'Images speaks thousand words',
        'photos' => $photos,
        'categories' => $categories,
        'activeCategory' => $category,
    ])->withViewData([
        'pageTitle' => 'Images speaks thousand words',
        'metaDesc' => config('app.alt_name') . ' photo gallery showcasing our events and activities.',
        'ogUrl' => route('app.gallery'),
        'canonical' => route('app.gallery'),
    ]);
}
```

Add import:

```php
use Modules\PublicPage\Models\EventPhoto;
```

### Step 6.2 — Rewrite Gallery.svelte

Replace the entire content of `Modules/PublicPage/resources/js/Pages/Gallery.svelte`:

```svelte
<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import { router } from '@inertiajs/svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app, photos, categories, activeCategory } = $page.props);

  let lightboxPhotoIndex = null;

  $: photoList = photos?.data || [];

  function filterByCategory(category) {
    router.get(route('app.gallery', category ? { category } : {}), {
      preserveState: true,
      preserveScroll: true,
      only: ['photos', 'activeCategory'],
    });
  }

  function loadMore() {
    router.get(photos.next_page_url, {
      preserveState: true,
      preserveScroll: true,
      only: ['photos'],
    });
  }

  function openLightbox(index) {
    lightboxPhotoIndex = index;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightboxPhotoIndex = null;
    document.body.style.overflow = '';
  }

  function prevPhoto() {
    if (lightboxPhotoIndex > 0) lightboxPhotoIndex--;
  }

  function nextPhoto() {
    if (lightboxPhotoIndex < photoList.length - 1) lightboxPhotoIndex++;
  }

  function handleKeydown(event) {
    if (lightboxPhotoIndex !== null) {
      if (event.key === 'Escape') closeLightbox();
      if (event.key === 'ArrowLeft') prevPhoto();
      if (event.key === 'ArrowRight') nextPhoto();
    }
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<PageTitle appName={app.name} pageTitle='Gallery'>
  <li class="breadcrumb-item active" aria-current="page">Gallery</li>
</PageTitle>

<section id="projectsGrid" class="projects projects-grid">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <ul class="projects-filter justify-content-center">
          <li>
            <a
              class="filter {!activeCategory ? 'active' : ''}"
              href="#"
              on:click|preventDefault={() => filterByCategory(null)}
            >All</a>
          </li>
          {#each categories as category}
            <li>
              <a
                class="filter {activeCategory === category ? 'active' : ''}"
                href="#"
                on:click|preventDefault={() => filterByCategory(category)}
              >{category}</a>
            </li>
          {/each}
        </ul>
      </div>
    </div>

    <div id="filtered-items-wrap" class="row">
      {#each photoList as photo, i}
        <div class="col-sm-6 col-md-6 col-lg-4 mix">
          <div class="project-item">
            <div class="project__img">
              <button on:click={() => openLightbox(i)} type="button" class="w-full">
                <img src={photo.thumbnail_url} alt={photo.alt_text || ''} class="img-fluid w-full" loading="lazy" />
              </button>
              <div class="service__overlay">
                <a href={route('events.detail', { slug: photo.event?.slug })} class="zoom__icon">
                  <i class="icon-link"></i>
                </a>
              </div>
            </div>
            <div class="project__content">
              <h4 class="project__title">
                <a href={route('events.detail', { slug: photo.event?.slug })}>
                  {photo.event?.name || 'Event'}
                </a>
              </h4>
              <div class="project__cat">
                <a href="#">{photo.event?.category || 'Uncategorized'}</a>
              </div>
            </div>
          </div>
        </div>
      {/each}

      {#if photoList.length === 0}
        <div class="col-12 text-center py-16">
          <p class="text-[#9b9b9b]">No photos found.</p>
        </div>
      {/if}
    </div>

    {#if photos?.next_page_url}
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
          <button on:click={loadMore} type="button" class="btn btn__primary btn__hover3 mt-20 loadMoreProjects">
            Load More
          </button>
        </div>
      </div>
    {/if}
  </div>
</section>

<!-- Photo Lightbox -->
{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photoList.length}
      </p>

      <img
        src={photoList[lightboxPhotoIndex].photo_url}
        alt={photoList[lightboxPhotoIndex].alt_text || ''}
        class="max-h-[80vh] mx-auto object-contain rounded-lg"
      />

      {#if photoList[lightboxPhotoIndex].alt_text}
        <p class="text-white text-center mt-4 text-sm opacity-80">{photoList[lightboxPhotoIndex].alt_text}</p>
      {/if}

      <p class="text-center mt-2">
        <a
          href={route('events.detail', { slug: photoList[lightboxPhotoIndex].event?.slug })}
          class="text-[#ff7607] hover:underline text-sm"
        >View event →</a>
      </p>

      {#if lightboxPhotoIndex > 0}
        <button on:click={prevPhoto} class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      {/if}
      {#if lightboxPhotoIndex < photoList.length - 1}
        <button on:click={nextPhoto} class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      {/if}
    </div>
  </div>
{/if}
```

Note: The gallery lightbox links to `route('events.detail', ...)`. Since we replaced `/events/{slug}` with the new EventDetail page, the `events.detail` named route doesn't exist — the gallery should link to `route('events.show', ...)` instead. The old gallery code may have used `app.gallery` as the route name. Verify the route name in `PublicPageController`: if the gallery method's Inertia render uses `route('app.gallery')`, the event detail link should use `route('events.show', ...)`.

---

## PHASE 7 — Fix `enhanced:img` across all 14 files

**Goal:** Replace all `enhanced:img` tags with working `<img>` tags. This is critical — every instance creates a broken `HTMLUnknownElement` that doesn't display.

### Step 7.1 — Files to modify

| # | File | Instances | Pattern |
|---|------|-----------|---------|
| 1 | `Header.svelte` | 2 | Static template assets → `getImgUrl()` |
| 2 | `Footer.svelte` | 1 | Static template assets → `getImgUrl()` |
| 3 | `PageTitle.svelte` | 1 | Already uses `getImgUrl()` for img src — just change `enhanced:img` to `img` |
| 4 | `BlogIndex.svelte` | 1 | Static → `getImgUrl()` |
| 5 | `Careers.svelte` | 2 | Static → `getImgUrl()` |
| 6 | `Awards.svelte` | 4 | Static → `getImgUrl()` |
| 7 | `SummarizedProjects.svelte` | 6 | Static → `getImgUrl()` |
| 8 | `SummarizedWhatWeDo.svelte` | 4 | Static → `getImgUrl()` |
| 9 | `SummarizedAbout.svelte` | 2 | Static → `getImgUrl()` |
| 10 | `OurPartners.svelte` | 6 | Static → `getImgUrl()` |
| 11 | `CTATwo.svelte` | 1 | Already has working `getImgUrl()` — just change `enhanced:img` to `img` |
| 12 | `Testimonials.svelte` | 1 | Already uses dynamic import — just change `enhanced:img` to `img` |
| 13 | `LaunchConference.svelte` (Conference module) | 6 | Static → `getImgUrl()` |
| 14 | `helpers.js` | 3 (comments/examples) | Update comments to show `<img>` not `enhanced:img` |

### Step 7.2 — Fix pattern

For each file:

1. **Replace element**: `enhanced:img` → `img`
2. **Remove `?enhanced` query** from the src value
3. **Use `getImgUrl()`** when the path is a static template asset:

   ```
   @publicpage-template/path/to/image.jpg?enhanced
   ```
   becomes:
   ```
   {getImgUrl('Modules/PublicPage/resources/template/assets/path/to/image.jpg')}
   ```

4. Add `import { getImgUrl } from '@/helpers';` to the script section if not already present

### Step 7.3 — Path mapping reference

| Alias path | getImgUrl key |
|------------|---------------|
| `@publicpage-template/images/logo/light.png?enhanced` | `'Modules/PublicPage/resources/template/assets/images/logo/light.png'` |
| `@publicpage-template/images/case-studies/grid/1.jpg?enhanced` | `'Modules/PublicPage/resources/template/assets/images/case-studies/grid/1.jpg'` |
| `@publicpage-template/images/services/judiciary.webp?enhanced` | `'Modules/PublicPage/resources/template/assets/images/services/judiciary.webp'` |
| etc. | Replace `@publicpage-template/` with `Modules/PublicPage/resources/template/assets/` |

### Step 7.4 — Already-imported files

Some files already import `getImgUrl`. For those, just change the element and the src path. Files known to already have the import:
- `PageTitle.svelte`
- `CTATwo.svelte`
- `Testimonials.svelte` (uses dynamic import pattern)

For files that don't have it, add the import.

### Step 7.5 — Build and verify

```bash
vendor/bin/sail bun run build
```

Build must complete with zero errors. Check output for any remaining `enhanced:img` warnings.

---

## PHASE 8 — Tests

**Goal:** Cover the new photo upload flow and updated public pages. Do not remove any existing tests.

### Step 8.1 — AdminPhotoControllerTest

```bash
vendor/bin/sail artisan make:test Admin/AdminPhotoControllerTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Create `Modules/PublicPage/tests/Feature/Admin/AdminPhotoControllerTest.php` with:

| Test | Assertion |
|------|-----------|
| Admin can upload single photo | 200 JSON, DB record created, file exists on disk |
| Admin can bulk-upload multiple photos | All records created, `sort_order` sequential |
| Non-admin cannot upload (403) | — |
| Unauthenticated cannot upload (401) | — |
| Rejects non-image file | 422 validation error |
| Rejects file over 10MB | 422 validation error |
| Admin can update alt_text | DB field updated |
| Admin can reorder photos | `sort_order` values updated per submitted array |
| Admin can delete photo | DB record gone, both files deleted from disk |

### Step 8.2 — GalleryPageTest

```bash
vendor/bin/sail artisan make:test GalleryPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Create `Modules/PublicPage/tests/Feature/GalleryPageTest.php` with:

| Test | Assertion |
|------|-----------|
| GET /gallery returns 200 | — |
| Photos appear when events have photos | — |
| Empty state when no photos exist | — |
| Filter by category | — |

### Step 8.3 — EventDetailPageTest

```bash
vendor/bin/sail artisan make:test EventDetailPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Create `Modules/PublicPage/tests/Feature/EventDetailPageTest.php` with:

| Test | Assertion |
|------|-----------|
| GET /events/{slug} returns 200 for published event | — |
| Response includes `photos` array | Ordered by `sort_order` |
| Response includes `videos` array | — |
| Event with no photos returns `photos = []` | — |
| Event with no videos returns `videos = []` | — |
| Unpublished event returns 404 | — |
| Photos + videos both present when event has both | — |

### Step 8.4 — Run all tests

```bash
vendor/bin/sail artisan test --compact --path=Modules/PublicPage/tests
```

All must pass before marking Phase 8 complete.

---

## PHASE 9 — Gallery image cleanup (manual)

> **Reminder for before this step:**
> The static placeholder images in `Modules/PublicPage/resources/template/assets/images/case-studies/grid/` are no longer displayed anywhere now that `/gallery` is dynamic.
>
> Before deleting them, decide:
> - **Are these real photos** from actual events? If yes → upload them through the admin panel to the correct event, then delete the files.
> - **Are they placeholder/stock images** that were never real content? If yes → delete the files directly.
>
> Once decided, either run the admin upload flow or:
> ```bash
> rm -rf Modules/PublicPage/resources/template/assets/images/case-studies/grid
> ```
>
> This step is intentionally manual.

---

## URL Structure

| URL | Before | After |
|-----|--------|-------|
| `/gallery` | Static image grid (hardcoded) | Dynamic photo grid from `event_photos`, filterable by category + lightbox |
| `/events/media-showcase` | Event cards (video count only) | Event cards (photo + video count badges) |
| `/events/{slug}` | Video-only grid + modal player | EventDetail: header → description → photo gallery (capped) → video playlist |
| `/events/{slug}/videos` | AJAX paginated videos | Unchanged (backward compat) |

## Storage Layout

```
storage/app/public/
  event-photos/
    {uuid}.{ext}            ← full-size (original upload)
    thumbnails/
      {uuid}.{ext}          ← 320×240 cover (Intervention Image)
  videos/
    …                       ← existing, unchanged
```

## Admin Workflow

| Task | Where |
|------|-------|
| Create event | Admin → Events → Create |
| Upload videos | Admin → Events → [event] → Videos tab |
| Upload photos | Admin → Events → [event] → Photos tab |
| Reorder photos | Drag handles in Photos tab |
| Set alt text | Inline input in Photos tab (saves on blur) |

## Success Criteria

- Admin can upload multiple photos to an event (up to 20 at once)
- Admin can edit alt text inline (on-blur)
- Admin can drag-drop reorder photos
- Admin can delete photos (files + record)
- `/gallery` shows dynamic photos from all published events, filterable by category, with lightbox + "View event" links
- `/events/{slug}` shows EventDetail: header → description → 12-photo preview (with "View all" cap) → side-by-side video playlist with retry logic
- `/events/media-showcase` shows photo + video count badges on event cards
- All `enhanced:img` tags replaced with working `<img>` tags across 14 files
- Images in header, footer, homepage, blog, awards, careers, etc. display correctly
- All existing tests pass
- New tests cover photo upload, gallery, and event detail pages

---

## Key Design Decisions

1. **`EventPhoto`** model name (not `Photo`) — avoids namespace collision. More explicit.

2. **`photo_url` / `thumbnail_url` / `alt_text`** column naming — explicit and accessibility-conscious.

3. **`photo_ids` array for reorder** — simpler than `[{id, sort_order}]`. Single array of IDs in new order.

4. **Flat `event-photos/` directory** — all photos in a flat directory with UUID filenames. Avoids path breakage if event is renamed or deleted + recreated.

5. **Gallery NOT redirected** — `/gallery` remains a photo-first browsing destination, distinct from the event-card-focused media showcase. Users wanting to browse photos land on a photo grid, not event cards.

6. **`/events/{slug}` replaced in-place** — no separate `/detail` route. The existing route URI stays the same; the rendered page changes from video-only grid to full EventDetail.

7. **Video retry logic preserved** — `MAX_RETRIES` / `handleVideoError` / `handleRetry` from `EventVideosGrid.svelte` carried into `EventVideoPlaylist.svelte`. Critical because videos go through async conversion pipeline.

8. **`enhanced:img` fix is part of this plan** — cannot ship a "gallery update" while the entire site's images (header, footer, homepage) are broken.

---

## Post-Implementation Notes

### Category Alignment

The old static gallery filter labels (**Protests, Awards, Charity Drive, Elections, Free Medicals**) are now added as official event categories. Combined with the existing ones:

**Conference, Workshop, Entertainment, Sports, Education, Other, Protests, Awards, Charity Drive, Elections, Free Medicals**

### Known Deferred Work

| Item | Reason deferred |
|------|----------------|
| S3 / CDN storage | Local disk sufficient for now; migrate when traffic demands |
| WebP / AVIF conversion | Intervention Image supports it; add when needed |
| Visible caption below photo | `alt_text` covers accessibility; visible caption can be added as a separate column later |
| Photo upload during event creation | Only available post-creation via Show page; add to Create form in a future PR if needed |

---

End of Plan.

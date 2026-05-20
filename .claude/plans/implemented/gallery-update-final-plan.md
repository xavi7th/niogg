# Gallery Update — Final Implementation Plan

> Merged from Sonnet + DeepSeek plans. All decisions resolved. Implement this file only.
> Supersedes `sonnet-gallery-update-plan.md` and `deepseek-gallery-update-plan.md`.

---

## SESSION STATE (UPDATE AFTER EACH SESSION)

```
CURRENT_PHASE: 9
STATUS: phases 1-8 complete, Phase 9 pending manual action. Test fixes complete.
LAST_UPDATED: 2026-05-19
```

### Phase Progress Table

| Phase | Title | Status | Date | Notes |
|-------|-------|--------|------|-------|
| 1 | Migration + EventPhoto model + Factory | ✅ | 2026-05-19 | |
| 1a | Event categories config + seeder | ✅ | 2026-05-19 | |
| 2 | EventPhotoUploadService + AdminPhotoController + Routes | ✅ | 2026-05-19 | |
| 3 | Admin UI: Tab switcher + AdminPhotosTab component | ✅ | 2026-05-19 | |
| 4 | Replace `/events/{slug}` with EventDetail page | ✅ | 2026-05-19 | |
| 5 | Media Showcase: photo count badges | ✅ | 2026-05-19 | |
| 6 | Rewrite `/gallery` as dynamic photo browsing page | ✅ | 2026-05-19 | |
| 7 | Fix `enhanced:img` across all 14 files | ✅ | 2026-05-19 | Also fixed mixed event handler syntax in Upload.svelte + class:/ issue in EventVideoPlaylist.svelte |
| 8 | Tests | ✅ | 2026-05-19 | 29 tests, 164 assertions. Also fixed: abort_if(!is_published) in EventsMediaShowcaseController@show |
| 9 | Gallery image cleanup (manual) | ⏳ | — | Needs human decision |
| — | **Test fixes (13 failing tests)** | ✅ | 2026-05-19 | Fixed RouteServiceProvider::home(), ProfileTest URLs, EmailVerificationTest route name, PasswordResetTest notification class |

---

## Architecture Overview

```
Events (already exists)
  ├── Videos (already exists)
  └── Photos (NEW via EventPhoto model)
        displayed on: /events/{slug}  (photo gallery + lightbox)
                     /gallery         (dynamic photo grid, category filter)

/gallery               → Rewritten: dynamic photo grid from event_photos, paginated, filterable
/events/media-showcase → Updated: photo + video count badges on event cards
/events/{slug}         → REPLACED: was video-only grid, now EventDetail:
                           EventHeader → description → photo gallery (capped) → video playlist
```

### Video Pipeline — NOT Affected

The existing video conversion pipeline (queued jobs, artisan commands, cron) is completely untouched.

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the SESSION STATE and progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (1 → 1a → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9). Never skip ahead.
2. **Use the exact code provided.** Adapt only the parts the plan explicitly says to adapt.
3. **Do not create any file not listed in this plan.**
4. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
5. **All Artisan/Composer/Node commands must go through Sail.**
6. **Run `vendor/bin/sail bin pint --dirty` after each phase.**
7. **Check sibling files for conventions** before writing any new PHP file.

---

## PHASE 1 — Migration + EventPhoto Model + Factory

**Goal:** Database table, Eloquent model, relationship on Event, factory for testing.

### Step 1.1 — Create migration

```bash
vendor/bin/sail artisan make:migration create_event_photos_table --path=Modules/PublicPage/database/migrations --no-interaction
```

Edit the generated file:

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

            $table->index(['event_id', 'sort_order']);
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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function scopeOrdered($query): void
    {
        $query->orderBy('sort_order')->orderBy('created_at');
    }
}
```

### Step 1.3 — Add `photos()` relationship to Event model

Edit `Modules/PublicPage/app/Models/Event.php`. Add import:

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

Add `HasMany` import if not already present:

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
            'event_id'      => Event::factory(),
            'photo_url'     => '/storage/event-photos/' . $this->faker->uuid() . '.jpg',
            'thumbnail_url' => '/storage/event-photos/thumbnails/' . $this->faker->uuid() . '.jpg',
            'alt_text'      => $this->faker->optional()->sentence(4),
            'sort_order'    => 0,
        ];
    }
}
```

---

## PHASE 1a — Event Categories Config + Seeder

**Goal:** Establish canonical event categories. Config file is the source of truth (accessible anywhere via `config()`). Seeder creates placeholder events for dev. Frontend forms become `<select>` dropdowns. Validation enforces the list.

### Step 1a.1 — Create config file

Create `config/event_categories.php`:

```php
<?php

return [
    'Conference',
    'Workshop',
    'Entertainment',
    'Sports',
    'Education',
    'Protests',
    'Awards',
    'Charity Drive',
    'Elections',
    'Free Medicals',
    'Other',
];
```

### Step 1a.2 — Create EventCategorySeeder (module-scoped)

Create `Modules/PublicPage/database/seeders/EventCategorySeeder.php`:

```php
<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('event_categories') as $category) {
            if (Event::where('category', $category)->exists()) {
                continue;
            }

            Event::factory()->create([
                'name'         => $category . ' Event (Placeholder)',
                'category'     => $category,
                'is_published' => false,
            ]);
        }
    }
}
```

### Step 1a.3 — Run seeder (dev only)

```bash
vendor/bin/sail artisan db:seed --class="Modules\\PublicPage\\Database\\Seeders\\EventCategorySeeder" --no-interaction
```

### Step 1a.4 — Update EventFormRequest validation

Edit `Modules/PublicPage/app/Http/Requests/Admin/EventFormRequest.php`. Add import:

```php
use Illuminate\Validation\Rule;
```

Update the `category` rule:

```php
'category' => ['required', 'string', Rule::in(config('event_categories'))],
```

### Step 1a.5 — Update admin Create/Edit forms — category select

In both `Admin/Events/Create.svelte` and `Admin/Events/Edit.svelte`:

**Read the files first.** Then in each controller action (`create`, `edit`), add:

```php
'categories' => config('event_categories'),
```

In the Svelte pages, replace any free-text `<input>` for category with:

```svelte
<select bind:value={formData.category}>
  <option value="">Select category</option>
  {#each categories as cat}
    <option value={cat}>{cat}</option>
  {/each}
</select>
```

---

## PHASE 2 — EventPhotoUploadService + AdminPhotoController + Routes

**Goal:** Service for upload/delete, controller with CRUD, admin routes.

### Step 2.1 — Check Intervention Image is installed

```bash
vendor/bin/sail composer show | grep intervention
```

If not listed:

```bash
vendor/bin/sail composer require intervention/image --no-interaction
```

### Step 2.2 — Create EventPhotoUploadService

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
    private const DISK         = 'public';
    private const PHOTO_PATH   = 'event-photos';
    private const THUMB_PATH   = 'event-photos/thumbnails';
    private const THUMB_WIDTH  = 320;
    private const THUMB_HEIGHT = 240;

    public function store(UploadedFile $file, Event $event, int $sortOrder): EventPhoto
    {
        $uuid      = (string) Str::uuid();
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
            'photo_url'     => Storage::disk(self::DISK)->url($photoPath),
            'thumbnail_url' => Storage::disk(self::DISK)->url($thumbRelative),
            'sort_order'    => $sortOrder,
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

### Step 2.3 — Create StoreEventPhotosRequest

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
            'photos'      => ['required', 'array', 'min:1', 'max:20'],
            'photos.*'    => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'alt_texts'   => ['nullable', 'array'],
            'alt_texts.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.max'     => 'You can upload up to 20 photos at once.',
            'photos.*.max'   => 'Each photo must be 10 MB or smaller.',
            'photos.*.image' => 'Each file must be an image (jpeg, png, jpg, gif, or webp).',
        ];
    }
}
```

### Step 2.4 — Create UpdateEventPhotoRequest

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

### Step 2.5 — Create AdminPhotoController

```bash
vendor/bin/sail artisan make:controller Admin/AdminPhotoController --no-interaction
```

Edit `Modules/PublicPage/app/Http/Controllers/Admin/AdminPhotoController.php`:

```php
<?php

namespace Modules\PublicPage\Http\Controllers\Admin;

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
    public function __construct(
        private readonly EventPhotoUploadService $photoService,
    ) {}

    public function store(StoreEventPhotosRequest $request, Event $event): JsonResponse
    {
        $altTexts   = $request->input('alt_texts', []);
        $startOrder = $event->photos()->max('sort_order') + 1;
        $photos     = [];

        foreach ($request->file('photos', []) as $index => $file) {
            $photo = $this->photoService->store($file, $event, $startOrder + $index);

            if (isset($altTexts[$index])) {
                $photo->update(['alt_text' => $altTexts[$index]]);
            }

            $photos[] = $photo;
        }

        return response()->json([
            'message' => count($photos) . ' photo(s) uploaded successfully.',
            'photos'  => $photos,
        ]);
    }

    public function update(UpdateEventPhotoRequest $request, EventPhoto $photo): JsonResponse
    {
        $photo->update($request->validated());

        return response()->json([
            'message' => 'Photo updated.',
            'photo'   => $photo->fresh(),
        ]);
    }

    public function reorder(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'photo_ids'   => ['required', 'array'],
            'photo_ids.*' => ['integer', 'exists:event_photos,id'],
        ]);

        foreach ($request->photo_ids as $index => $photoId) {
            EventPhoto::where('id', $photoId)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Photos reordered.']);
    }

    public function destroy(EventPhoto $photo): JsonResponse
    {
        $this->photoService->delete($photo);
        $photo->delete();

        return response()->json(['message' => 'Photo deleted.']);
    }
}
```

### Step 2.6 — Add admin routes

Edit `Modules/PublicPage/routes/web.php`. Add import:

```php
use Modules\PublicPage\Http\Controllers\Admin\AdminPhotoController;
```

Inside the `admin` middleware group, after the videos section:

```php
Route::prefix('events')->name('events.')->group(function (): void {
    Route::post('/{event}/photos',         [AdminPhotoController::class, 'store'])  ->name('photos.store');
    Route::post('/{event}/photos/reorder', [AdminPhotoController::class, 'reorder'])->name('photos.reorder');
});

Route::prefix('photos')->name('photos.')->group(function (): void {
    Route::put('/{photo}',    [AdminPhotoController::class, 'update']) ->name('update');
    Route::delete('/{photo}', [AdminPhotoController::class, 'destroy'])->name('destroy');
});
```

Named routes produced:
- `admin.events.photos.store`
- `admin.events.photos.reorder`
- `admin.photos.update`
- `admin.photos.destroy`

---

## PHASE 3 — Admin UI: Tab Switcher + AdminPhotosTab Component

**Goal:** Add Photos tab to the event show page with full photo management.

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

  function handlePhotoDragStart(photoId, e) {
    photoDragId = photoId;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', photoId.toString());
    e.target.closest('.group').style.opacity = '0.5';
  }

  function handlePhotoDragEnd(e) {
    photoDragId = null;
    photoDragOverId = null;
    if (e.target.closest) e.target.closest('.group').style.opacity = '1';
  }

  function handlePhotoDragOver(photoId, e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    if (photoDragId !== photoId) photoDragOverId = photoId;
  }

  function handlePhotoDragLeave(photoId) {
    if (photoDragOverId === photoId) photoDragOverId = null;
  }

  async function handlePhotoDrop(photoId, e) {
    e.preventDefault();
    photoDragOverId = null;
    if (photoDragId === null || photoDragId === photoId) return;

    const draggedIndex = sortedPhotos.findIndex((p) => p.id === photoDragId);
    const dropIndex = sortedPhotos.findIndex((p) => p.id === photoId);
    if (draggedIndex === -1 || dropIndex === -1) return;

    const newOrder = [...sortedPhotos];
    const [removed] = newOrder.splice(draggedIndex, 1);
    newOrder.splice(dropIndex, 0, removed);

    isPhotoReordering = true;
    try {
      const response = await fetch(`/admin/events/${event.id}/photos/reorder`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ photo_ids: newOrder.map((p) => p.id) }),
      });
      if (response.ok) router.reload({ only: ['event'] });
    } catch (err) {
      console.error('Reorder failed', err);
    } finally {
      isPhotoReordering = false;
      photoDragId = null;
    }
  }

  function handlePhotoSelect(e) {
    addPhotoFiles(e.target.files);
    e.target.value = '';
  }

  function handlePhotoDropEvent(e) {
    addPhotoFiles(e.dataTransfer.files);
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
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
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
    <h2 class="text-lg font-semibold text-[#1b1a1a]">Photos ({sortedPhotos.length})</h2>
    {#if sortedPhotos.length > 1}
      <p class="text-sm text-[#9b9b9b] mt-1">Drag photos to reorder them</p>
    {/if}
  </div>
  <button
    on:click={() => (showPhotoUpload = true)}
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
      on:click={() => (showPhotoUpload = true)}
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
                <button
                  on:click|stopPropagation={() => removeNewPhoto(i)}
                  type="button"
                  class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            {/each}
          </div>
        {/if}

        <p class="text-[#9b9b9b]">Click or drag photos here</p>
        <p class="text-xs text-[#9b9b9b] mt-1">JPEG, PNG, GIF, WebP · Max 10 MB each · Up to 20 at once</p>
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
          {isUploading ? 'Uploading…' : `Upload ${newPhotoFiles.length} photo${newPhotoFiles.length !== 1 ? 's' : ''}`}
        </button>
      </div>
    </div>
  </div>
{/if}

{#if showPhotoDeleteDialog}
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
      <h3 class="text-lg font-semibold text-[#1b1a1a] mb-2">Delete Photo</h3>
      <p class="text-[#9b9b9b] mb-2">Are you sure you want to delete this photo?</p>
      {#if photoToDelete?.alt_text}
        <p class="text-sm text-[#1b1a1a] mb-4 italic">"{photoToDelete.alt_text}"</p>
      {/if}
      <p class="text-xs text-red-500 mb-4">This cannot be undone. The file will be permanently deleted from storage.</p>
      <div class="flex justify-end gap-3">
        <button on:click={closePhotoDeleteDialog} type="button" class="px-4 py-2 border border-[#eaeaea] rounded-lg text-[#1b1a1a] hover:bg-[#f9f9f9] font-medium text-sm">Cancel</button>
        <button on:click={confirmDeletePhoto} type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium text-sm">Delete</button>
      </div>
    </div>
  </div>
{/if}
```

### Step 3.2 — Update Admin Event Show page with tab switcher

**Read `Modules/PublicPage/resources/js/Pages/Admin/Events/Show.svelte` in full before editing.**

Add to `<script>`:

```js
import AdminPhotosTab from '@publicpage-components/Admin/AdminPhotosTab.svelte';
let activeTab = 'videos';
```

Replace the existing Videos section heading + content with:

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
      on:click={() => (activeTab = 'videos')}
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
      on:click={() => (activeTab = 'photos')}
      type="button"
    >
      Photos ({event?.photos?.length || 0})
    </button>
  </nav>
</div>

{#if activeTab === 'videos'}
  ... existing video management content unchanged ...
{/if}

{#if activeTab === 'photos'}
  <AdminPhotosTab {event} />
{/if}
```

### Step 3.3 — Eager-load photos on admin event show

Edit `AdminEventController@show`:

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

## PHASE 4 — Replace `/events/{slug}` with EventDetail Page

**Goal:** Replace the video-only EventVideosGrid with EventDetail — header → description → photo gallery → video playlist. Route name `events.show` is unchanged.

### Step 4.1 — Update `EventsMediaShowcaseController@show`

Edit `Modules/PublicPage/app/Http/Controllers/EventsMediaShowcaseController.php`. Replace `show()`:

```php
public function show(Event $event)
{
    $event->load([
        'photos' => function ($query): void {
            $query->ordered();
        },
        'videos' => function ($query): void {
            $query->ordered();
        },
    ]);

    return Inertia::render('PublicPage::EventDetail', [
        'event'     => $event,
        'pageTitle' => $event->name,
    ]);
}
```

Load ALL videos (no pagination) — the playlist needs the full list for up-next navigation. The `eventVideos()` AJAX endpoint for backward compat is left untouched.

### Step 4.2 — Rename EventVideosGrid → EventDetail

```bash
mv Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte \
   Modules/PublicPage/resources/js/Pages/EventDetail.svelte
```

### Step 4.3 — Create EventPhotoGallery component

Create `Modules/PublicPage/resources/js/Components/EventPhotoGallery.svelte`:

```svelte
<script>
  export let photos = [];

  const PREVIEW_CAP = 12;

  let lightboxPhotoIndex = null;

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

  function handleKeydown(e) {
    if (lightboxPhotoIndex === null) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Photo Gallery</h2>

<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
  {#each photos.slice(0, PREVIEW_CAP) as photo, i}
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

{#if photos.length > PREVIEW_CAP}
  <div class="text-center mt-6">
    <button
      on:click={() => openLightbox(0)}
      class="px-6 py-3 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium transition-colors"
      type="button"
    >
      View all {photos.length} photos
    </button>
  </div>
{/if}

{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
    aria-modal="true"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photos.length}
      </p>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

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

**Behaviour:**
- Grid: 2 cols mobile → 3 cols tablet → 4 cols desktop
- Preview cap: 12 photos shown in grid
- "View all N photos" button → opens lightbox at index 0; user navigates all N via arrows
- Lightbox: full-size `photo_url`, caption from `alt_text`, counter (3 / 47), keyboard nav, backdrop close

### Step 4.4 — Create EventVideoPlaylist component

Create `Modules/PublicPage/resources/js/Components/EventVideoPlaylist.svelte`:

```svelte
<script>
  export let videos = [];

  let activeVideoId = null;
  let videoError = null;
  let retryCount = 0;
  const MAX_RETRIES = 3;
  let playerRef;
  let playerColumnHeight = 0;
  let playlistItemRefs = {};

  $: activeVideo = videos.find((v) => v.id === activeVideoId) ?? videos[0] ?? null;
  $: activeVideoIndex = videos.findIndex((v) => v.id === activeVideoId);

  $: if (videos.length > 0 && !activeVideoId) {
    activeVideoId = videos[0].id;
  }

  $: if (activeVideoId) {
    videoError = null;
    retryCount = 0;
  }

  // Auto-scroll active playlist row into view
  $: if (activeVideoId && playlistItemRefs[activeVideoId]) {
    playlistItemRefs[activeVideoId].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function setActiveVideo(video) {
    activeVideoId = video.id;
    videoError = null;
    retryCount = 0;
    if (window.innerWidth < 1024) {
      playerRef?.scrollIntoView({ behavior: 'smooth' });
    }
  }

  function nextVideo() {
    const idx = videos.findIndex((v) => v.id === activeVideoId);
    if (idx < videos.length - 1) {
      activeVideoId = videos[idx + 1].id;
    }
  }

  const handleVideoError = (e) => {
    const code = e.target?.error?.code ?? null;
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = { message: 'Error loading video.', retryable: retryCount < MAX_RETRIES };
    } else {
      videoError = { message: getVideoErrorMessage(code), retryable: false };
    }
  };

  const getVideoErrorMessage = (code) => {
    if (code === 2) return 'Network error loading video. Check your connection.';
    if (code === 3) return 'Video could not be decoded. File may be corrupted.';
    return 'An unexpected error occurred loading this video.';
  };

  const handleRetry = () => {
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = null;
    }
  };

  function formatDuration(seconds) {
    if (!seconds) return '';
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
  }
</script>

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Videos</h2>

<div class="flex flex-col lg:flex-row gap-6 items-start">

  <!-- Player — 65% on desktop -->
  <div class="w-full lg:w-[65%]" bind:this={playerRef} bind:clientHeight={playerColumnHeight}>
    {#if activeVideo}
      <div class="bg-black rounded-xl overflow-hidden">
        {#if videoError}
          <div class="aspect-video flex flex-col items-center justify-center bg-gray-900 text-white p-6">
            <p class="text-red-400 mb-4">{videoError.message}</p>
            {#if videoError.retryable}
              <button
                on:click={handleRetry}
                type="button"
                class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
              >
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

  <!-- Playlist — 35% on desktop, max-height matches player column via bind:clientHeight -->
  <div class="w-full lg:w-[35%]">
    <h4 class="font-medium text-[#1b1a1a] mb-3">Up Next</h4>
    <div
      class="space-y-1 overflow-y-auto pr-1"
      style="max-height: {playerColumnHeight > 0 ? playerColumnHeight + 'px' : '70vh'}"
    >
      {#each videos as video, i}
        <button
          bind:this={playlistItemRefs[video.id]}
          on:click={() => setActiveVideo(video)}
          type="button"
          class="w-full flex gap-3 p-2 rounded-lg transition-colors text-left border-l-4"
          class:border-[#ff7607]={activeVideo?.id === video.id}
          class:bg-[#ff7607]/10={activeVideo?.id === video.id}
          class:border-transparent={activeVideo?.id !== video.id}
          class:hover:bg-gray-50={activeVideo?.id !== video.id}
        >
          <div class="w-[60px] h-[45px] flex-shrink-0 bg-gray-100 rounded overflow-hidden">
            {#if video.thumbnail_url}
              <img src={video.thumbnail_url} alt={video.title} class="w-full h-full object-cover" loading="lazy" />
            {:else}
              <div class="w-full h-full flex items-center justify-center text-[#9b9b9b]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
              </div>
            {/if}
          </div>
          <div class="flex-1 min-w-0">
            <p
              class="text-sm leading-snug"
              class:font-semibold={activeVideo?.id === video.id}
              class:text-[#ff7607]={activeVideo?.id === video.id}
              class:font-medium={activeVideo?.id !== video.id}
              class:text-[#1b1a1a]={activeVideo?.id !== video.id}
            >
              {#if activeVideo?.id === video.id}▶ {/if}{i + 1}. {video.title}
            </p>
            {#if video.duration_seconds}
              <p class="text-xs text-[#9b9b9b] mt-0.5">{formatDuration(video.duration_seconds)}</p>
            {/if}
          </div>
        </button>
      {/each}
    </div>
  </div>

</div>
```

**Layout matches ASCII diagram:**
- Desktop: 65% player + 35% playlist (proportional, not fixed px)
- `bind:clientHeight` on player column → `max-height` on playlist tracks it dynamically
- `▶` prefix on active row title
- Active row: `scrollIntoView` reactive, orange left border + tinted background + semibold
- Mobile click → scrolls player into view

### Step 4.5 — Rewrite EventDetail.svelte

Replace the content of the renamed file:

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
        <a href={route('events.media-showcase')} class="text-[#ff7607] hover:underline mt-2 inline-block">
          Back to events
        </a>
      </div>
    {/if}
  </div>
</section>
```

---

## PHASE 5 — Media Showcase: Photo Count Badges

**Goal:** Show `📷 N ▶ N` badges per event card on the showcase index.

### Step 5.1 — Add `withCount` to index query

Edit `EventsMediaShowcaseController@index`. Replace existing query:

```php
$events = Event::withCount(['videos', 'photos'])
    ->published()
    ->ordered($direction)
    ->get();
```

### Step 5.2 — Add badge to event cards

**Read `EventsMediaShowcase.svelte` first.** Find where event cards render. Add:

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

## PHASE 6 — Rewrite `/gallery` as Dynamic Photo Browsing Page

**Goal:** Replace the static 6-image gallery with a dynamic paginated photo grid from `event_photos`, filterable by category, with lightbox and Load More that **appends** (preserving lightbox navigation across pages).

### Step 6.1 — Rewrite `gallery()` in PublicPageController

Edit `Modules/PublicPage/app/Http/Controllers/PublicPageController.php`. Replace `gallery()`:

```php
public function gallery(): \Inertia\Response
{
    $category = request('category');

    $photos = \Modules\PublicPage\Models\EventPhoto::query()
        ->whereHas('event', fn ($q) => $q->published())
        ->with('event:id,name,slug,category')
        ->when($category, fn ($q, $c) => $q->whereHas('event', fn ($inner) => $inner->where('category', $c)))
        ->ordered()
        ->latest()
        ->paginate(24);

    $categories = \Modules\PublicPage\Models\Event::published()
        ->whereHas('photos')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return Inertia::render('PublicPage::Gallery', [
        'pageTitle'      => 'Images speaks thousand words',
        'photos'         => $photos,
        'categories'     => $categories,
        'activeCategory' => $category,
    ])->withViewData([
        'pageTitle' => 'Images speaks thousand words',
        'metaDesc'  => config('app.alt_name') . ' photo gallery showcasing our events and activities.',
        'ogUrl'     => route('app.gallery'),
        'canonical' => route('app.gallery'),
    ]);
}
```

### Step 6.2 — Rewrite Gallery.svelte

**Read `Modules/PublicPage/resources/js/Pages/Gallery.svelte` in full before editing.** Preserve the outer layout structure (`.projects-grid`, `.container`, `.row`, Bootstrap grid classes).

Replace the script and dynamic content:

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

  let photoList = [];
  let lightboxPhotoIndex = null;
  let isAppending = false;

  // Replace photoList on category change; append on Load More
  $: if (photos?.data && !isAppending) {
    photoList = photos.data;
  }

  function filterByCategory(category) {
    isAppending = false;
    router.get(
      route('app.gallery', category ? { category } : {}),
      {},
      { preserveState: true, preserveScroll: true, only: ['photos', 'activeCategory'] }
    );
  }

  function loadMore() {
    if (!photos?.next_page_url) return;
    isAppending = true;
    router.visit(photos.next_page_url, {
      method: 'get',
      preserveState: true,
      preserveScroll: true,
      only: ['photos'],
      onSuccess: () => {
        photoList = [...photoList, ...(photos.data ?? [])];
        isAppending = false;
      },
      onError: () => {
        isAppending = false;
      },
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

  function handleKeydown(e) {
    if (lightboxPhotoIndex === null) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<PageTitle appName={app.name} pageTitle="Gallery">
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
                <a href={route('events.show', { event: photo.event?.slug })} class="zoom__icon">
                  <i class="icon-link"></i>
                </a>
              </div>
            </div>
            <div class="project__content">
              <h4 class="project__title">
                <a href={route('events.show', { event: photo.event?.slug })}>
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

{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
    aria-modal="true"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photoList.length}
      </p>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

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
          href={route('events.show', { event: photoList[lightboxPhotoIndex].event?.slug })}
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

**Load More appends** — `isAppending` flag prevents the reactive `$:` from replacing `photoList`. `onSuccess` callback manually appends the new page. Lightbox counter shows `photoList.length` which grows as pages load.

**Route name** — `route('events.show', { event: photo.event?.slug })` (confirmed: route defined in web.php as `Route::get('/events/{event:slug}', ...)->name('events.show')`).

---

## PHASE 7 — Fix `enhanced:img` Across All 14 Files

**Goal:** Replace all `<enhanced:img>` tags with working `<img>` tags. Every instance produces a broken `HTMLUnknownElement` at runtime.

### Step 7.1 — Files to modify

| # | File | Pattern |
|---|------|---------|
| 1 | `Header.svelte` | Static assets → `getImgUrl()` |
| 2 | `Footer.svelte` | Static assets → `getImgUrl()` |
| 3 | `PageTitle.svelte` | Already uses `getImgUrl()` — just change tag |
| 4 | `BlogIndex.svelte` | Static → `getImgUrl()` |
| 5 | `Careers.svelte` | Static → `getImgUrl()` |
| 6 | `Awards.svelte` | Static → `getImgUrl()` |
| 7 | `SummarizedProjects.svelte` | Static → `getImgUrl()` |
| 8 | `SummarizedWhatWeDo.svelte` | Static → `getImgUrl()` |
| 9 | `SummarizedAbout.svelte` | Static → `getImgUrl()` |
| 10 | `OurPartners.svelte` | Static → `getImgUrl()` |
| 11 | `CTATwo.svelte` | Already has `getImgUrl()` — just change tag |
| 12 | `Testimonials.svelte` | Already uses dynamic import — just change tag |
| 13 | `LaunchConference.svelte` (Conference module) | Static → `getImgUrl()` |
| 14 | `helpers.js` | Update comments/examples |

### Step 7.2 — Fix pattern

For each file:

1. **Read it first.**
2. Replace element: `enhanced:img` → `img`
3. Remove `?enhanced` query from src
4. Use `getImgUrl()` for static template assets:
   ```
   @publicpage-template/path/image.jpg?enhanced
   ```
   becomes:
   ```
   {getImgUrl('Modules/PublicPage/resources/template/assets/path/image.jpg')}
   ```
5. Add `import { getImgUrl } from '@/helpers';` if not already present

Alias mapping: replace `@publicpage-template/` with `Modules/PublicPage/resources/template/assets/`

### Step 7.3 — Build and verify

```bash
vendor/bin/sail bun run build
```

Build must complete with zero errors. Check for any remaining `enhanced:img` warnings.

---

## PHASE 8 — Tests

**Goal:** Cover all new behaviour. Do not remove any existing tests.

### Step 8.1 — AdminPhotoControllerTest

```bash
vendor/bin/sail artisan make:test Admin/AdminPhotoControllerTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

| Test | Assertion |
|------|-----------|
| Admin can upload single photo | 200 JSON, DB record created, files exist on disk |
| Admin can bulk-upload multiple photos | All records created, `sort_order` sequential |
| Alt texts set on upload when provided | `alt_text` populated on each photo |
| Non-admin cannot upload | 403 |
| Unauthenticated cannot upload | 401 |
| Rejects non-image file | 422 |
| Rejects file over 10 MB | 422 |
| Admin can update `alt_text` | DB field updated |
| Admin can reorder photos | `sort_order` values match submitted array |
| Admin can delete photo | DB record gone, both files deleted from disk |

### Step 8.2 — GalleryPageTest

```bash
vendor/bin/sail artisan make:test GalleryPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

| Test | Assertion |
|------|-----------|
| GET /gallery returns 200 | — |
| Response includes `photos` paginated data | — |
| Response includes `categories` from published events with photos | — |
| Filter by category returns only matching photos | — |
| Empty state when no photos exist | `photos.data` is empty array |
| Photos from unpublished events excluded | — |

### Step 8.3 — EventDetailPageTest

```bash
vendor/bin/sail artisan make:test EventDetailPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

| Test | Assertion |
|------|-----------|
| GET /events/{slug} returns 200 for published event | — |
| Response includes `photos` ordered by `sort_order` | — |
| Response includes `videos` ordered by `sort_order` | — |
| Event with no photos returns `photos = []` | — |
| Event with no videos returns `videos = []` | — |
| Unpublished event returns 404 | — |

### Step 8.4 — Update MediaShowcaseIndexTest (if exists)

Add: index response includes `photos_count` and `videos_count` on each event object.

### Step 8.5 — Run tests

```bash
vendor/bin/sail artisan test --compact --filter=AdminPhotoControllerTest --no-interaction
vendor/bin/sail artisan test --compact --filter=GalleryPageTest --no-interaction
vendor/bin/sail artisan test --compact --filter=EventDetailPageTest --no-interaction
```

All must pass before marking Phase 8 complete.

---

## PHASE 9 — Gallery Image Cleanup (Manual)

> **Human decision required. Do not automate.**
>
> Static placeholder images at `Modules/PublicPage/resources/template/assets/images/case-studies/grid/` are no longer rendered anywhere now that `/gallery` is dynamic.
>
> Decide:
> - **Real photos from actual events?** → Upload via Admin → Events → [event] → Photos tab. Then delete the files.
> - **Placeholder/stock images never used as real content?** → Delete directly.
>
> ```bash
> rm -rf Modules/PublicPage/resources/template/assets/images/case-studies/grid/
> ```

---

## Success Criteria

| # | Criterion |
|---|-----------|
| 1 | Admin can upload photos (up to 20 at once) with optional alt text set at upload time |
| 2 | Thumbnails (320×240) generated server-side for every uploaded photo |
| 3 | Admin can edit alt text inline (on-blur), reorder (drag-drop), delete photos |
| 4 | Event categories restricted to the canonical list via `config/event_categories.php` |
| 5 | Public event detail page shows header → description → photo gallery → video playlist |
| 6 | Photo gallery preview shows max 12; "View all" opens lightbox navigating all photos |
| 7 | Video section is a 65/35 playlist — inline player, active row tracks with `scrollIntoView`, auto-advance, retry logic |
| 8 | `/gallery` shows dynamic paginated photo grid, category-filterable, Load More appends, lightbox has "View event →" link |
| 9 | Media showcase index shows photo + video count badges per event |
| 10 | All `enhanced:img` tags replaced; `bun run build` completes with zero errors |
| 11 | All new tests pass; existing test suite continues to pass |

---

## URL Structure

| URL | Before | After |
|-----|--------|-------|
| `/gallery` | Static image grid (hardcoded) | Dynamic photo grid, filterable by category, paginated |
| `/events/media-showcase` | Event cards (video count only) | Event cards (photo + video count badges) |
| `/events/{slug}` | Video-only grid + modal | EventDetail: header → photos → playlist |

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
| Set alt text | Inline input in Photos tab (saves on blur) OR at upload time |
| Reorder photos | Drag handles in Photos tab |

## Deferred Work

| Item | Reason |
|------|--------|
| S3 / CDN storage | Local disk sufficient for now |
| WebP / AVIF conversion | Intervention Image supports it; add when needed |
| Photo upload during event creation | Only via Show page; add to Create form later if needed |

---

End of Plan.

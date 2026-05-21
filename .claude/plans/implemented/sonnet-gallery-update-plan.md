# Event Photo Gallery — Unified Media Hub

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
| 1 | Migration — `create_event_photos_table` | ⏳ | — | |
| 2 | `EventPhoto` model + Event relationship + Factory | ⏳ | — | |
| 3 | `EventPhotoUploadService` (with thumbnail generation) | ⏳ | — | |
| 4 | `AdminPhotoController` + Form Requests | ⏳ | — | |
| 5 | Admin routes | ⏳ | — | |
| 6 | Admin UI — Photos tab on Event Show page | ⏳ | — | |
| 7 | Event category config + seeder | ⏳ | — | |
| 8 | Public controller — eager load photos + `withCount` for showcase | ⏳ | — | |
| 9 | Public UI — `EventDetail`, `EventPhotoGallery`, `EventVideoPlaylist` | ⏳ | — | |
| 10 | Rewrite `/gallery` as dynamic Photo Gallery | ⏳ | — | |
| 11 | Tests | ⏳ | — | |
| 12 | Fix `enhanced:img` across Svelte files | ⏳ | — | |
| 13 | Gallery image cleanup (manual — user action required) | ⏳ | — | |

---

## Critical Questions — All Answered ✅

| # | Question | Answer |
|---|----------|--------|
| 1 | Separate table or JSON column? | ✅ **Separate `event_photos` table** — normalised, sortable, individually manageable |
| 2 | Table / model name? | ✅ **`event_photos` / `EventPhoto`** — scoped naming avoids future collision with any generic Photo concept |
| 3 | Storage disk? | ✅ **`public` disk** — same as videos, at `storage/app/public/event-photos/` |
| 4 | Thumbnail generation? | ✅ **Yes — Intervention Image** — generates 320×240 thumbnail alongside full-size. `photo_url` + `thumbnail_url` separate columns |
| 5 | `caption` or `alt_text`? | ✅ **`alt_text`** — semantically correct for accessibility; visible caption not needed |
| 6 | `/gallery` URL — keep or retire? | ✅ **Keep + rewrite as dynamic Photo Gallery** — serves visitors who want to browse photos without the event-centric view. Category-filterable, paginated grid. "View event →" links cross to `/events/{slug}`. |
| 7 | Photo fatigue on event detail? | ✅ **12-photo cap** — preview grid shows max 12; "View all X photos" button opens full lightbox. Videos always reachable without infinite scroll |
| 8 | Video layout on event detail? | ✅ **Playlist** — inline player (65/35 desktop, stacked mobile) with auto-advance on `ended`. No modal |
| 9 | Photo upload flow (admin)? | ✅ **Separate from video** — Photos tab alongside Videos tab on Event Show page. Keeps existing video tagging/detailing intact |
| 10 | Event categories — canonical set? | ✅ **Old gallery filter labels** — Protests, Awards, Charity Drive, Elections, Free Medicals. Added via config + seeder |
| 11 | Photo count badge on media showcase index? | ✅ **Yes** — `📷 N  ▶ N` badge per event card, shown only for counts > 0 |
| 12 | Photo limit per event? | ✅ **None enforced server-side** — 12-photo cap is a UX guard, not a hard limit |
| 13 | Static gallery images deleted? | ✅ **No** — preserved pending user decision (see Phase 13) |
| 14 | `enhanced:img` fix? | ✅ **Yes** — Phase 12, after all feature work, before manual cleanup reminder |

---

## Architecture Overview

```
Events (exists)
  ├── Videos (exists) → /events/media-showcase (timeline + grid)
  │                  → /events/{slug}  (playlist player — Phase 9)
  └── Photos (NEW)   → /events/{slug}  (photo gallery — Phase 9)
                     → /events/media-showcase (photo count badge — Phase 8)

/gallery             → Rewritten: dynamic paginated photo grid, filterable by category (Phase 10)
/events/{slug}       → REPLACED: was video-only grid, now EventDetail:
                         header → description → photo gallery (capped) → video playlist
/events/media-showcase → existing, updated with photo count badges (Phase 8)
```

**Data flow:** Admin creates Event → uploads photos (Photos tab) + videos (Videos tab) separately → photos appear on event detail page (capped preview + full lightbox) → videos appear as ordered playlist on event detail page → both counts shown as badges on the media showcase index. One event, one canonical detail URL, zero duplication.

---

## Instructions for the Implementing AI

Read every word of this plan before writing a single line of code. Implement phase by phase in strict order. After each phase, update the SESSION STATE and progress table above.

### Rules — follow without exception

1. **Implement phases in strict order** (Phase 1 → 2 → … → 13). Never skip ahead.
2. **All commands run from repo root** (`/Users/leinad/Work/htdocs/asuke-niogg.org/`).
3. **All Artisan/Composer/Node commands must go through Sail:** `vendor/bin/sail artisan …`, `vendor/bin/sail composer …`, `vendor/bin/sail bun …`.
4. **Run `vendor/bin/sail bin pint --dirty` after each phase** to match project code style.
5. **Do not create any file not listed in this plan.**
6. **Do not modify any file not listed in this plan.**
7. **Check sibling files for conventions** before writing any new PHP or Svelte file.
8. **ALL code MUST use 2-space indentation.** Never use 4-space indentation. This applies to PHP, Svelte, TypeScript, JavaScript, YAML, and all other files.

---

## PHASE 1 — Migration: `create_event_photos_table`

**Goal:** Create the database table for event photos with full-size and thumbnail URL columns.

### Step 1.1 — Create migration

```bash
vendor/bin/sail artisan make:migration create_event_photos_table --path=Modules/PublicPage/database/migrations --no-interaction
```

### Step 1.2 — Edit the generated migration

Replace the generated file content with:

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
            $table->foreignId('event_id')->constrained()->cascadeOnDelete()->index();
            $table->string('photo_url');
            $table->string('thumbnail_url');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
    }
};
```

### Step 1.3 — Run migration

```bash
vendor/bin/sail artisan migrate --no-interaction
```

### Step 1.4 — Verify table exists

```bash
vendor/bin/sail artisan tinker --execute="echo Schema::hasTable('event_photos') ? 'OK' : 'MISSING';"
```

Expected output: `OK`

---

## PHASE 2 — `EventPhoto` Model + Event Relationship + Factory

**Goal:** Create the Eloquent model, wire the relationship on Event, and add a factory for testing.

### Step 2.1 — Create `EventPhoto` model

```bash
vendor/bin/sail artisan make:model EventPhoto --path=Modules/PublicPage/app/Models --no-interaction
```

Edit `Modules/PublicPage/app/Models/EventPhoto.php`:

```php
<?php

namespace Modules\PublicPage\Models;

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

### Step 2.2 — Add `photos()` relationship to Event model

Edit `Modules/PublicPage/app/Models/Event.php`.

Add import (if not already present):

```php
use Modules\PublicPage\Models\EventPhoto;
```

Add method after the existing `videos()` method:

```php
public function photos(): HasMany
{
    return $this->hasMany(EventPhoto::class)->orderBy('sort_order');
}
```

### Step 2.3 — Create `EventPhotoFactory`

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
            'photo_url'     => $this->faker->imageUrl(1200, 800, 'event'),
            'thumbnail_url' => $this->faker->imageUrl(320, 240, 'event'),
            'alt_text'      => $this->faker->sentence(4),
            'sort_order'    => 0,
        ];
    }
}
```

---

## PHASE 3 — `EventPhotoUploadService`

**Goal:** Service that stores uploaded images to the `public` disk and generates a 320×240 thumbnail via Intervention Image.

### Step 3.1 — Verify Intervention Image is installed

```bash
vendor/bin/sail composer show | grep intervention
```

If `intervention/image` is not listed, install it:

```bash
vendor/bin/sail composer require intervention/image --no-interaction
```

### Step 3.2 — Create `EventPhotoUploadService`

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
    private const DISK          = 'public';
    private const PHOTO_PATH    = 'event-photos';
    private const THUMB_PATH    = 'event-photos/thumbnails';
    private const THUMB_WIDTH   = 320;
    private const THUMB_HEIGHT  = 240;

    /**
     * Store one uploaded image + its thumbnail. Returns a persisted EventPhoto.
     */
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

    /**
     * Delete the photo and thumbnail files from disk.
     */
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

---

## PHASE 4 — `AdminPhotoController` + Form Requests

**Goal:** A dedicated controller for photo CRUD. Keeps `AdminEventController` clean (SRP).

### Step 4.1 — Create `StoreEventPhotosRequest`

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
        return true; // route already protected by auth + admin middleware
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

### Step 4.2 — Create `UpdateEventPhotoRequest`

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
        return true;
    }

    public function rules(): array
    {
        return [
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
```

### Step 4.3 — Create `AdminPhotoController`

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
    public function __construct(
        private readonly EventPhotoUploadService $photoService,
    ) {}

    public function store(
        StoreEventPhotosRequest $request,
        Event $event,
    ): JsonResponse {
        try {
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
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to upload photos.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(UpdateEventPhotoRequest $request, EventPhoto $photo): JsonResponse
    {
        try {
            $photo->update($request->validated());

            return response()->json(['photo' => $photo->fresh()]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update photo.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function reorder(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'photo_ids'   => ['required', 'array'],
            'photo_ids.*' => ['integer', 'exists:event_photos,id'],
        ]);

        try {
            foreach ($request->photo_ids as $index => $photoId) {
                EventPhoto::where('id', $photoId)->update(['sort_order' => $index]);
            }

            return response()->json(['message' => 'Photos reordered.']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to reorder photos.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(EventPhoto $photo): JsonResponse
    {
        try {
            $this->photoService->delete($photo);
            $photo->delete();

            return response()->json(['message' => 'Photo deleted.']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete photo.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
```

---

## PHASE 5 — Admin Routes

**Goal:** Register all photo management routes inside the existing `admin` middleware group.

### Step 5.1 — Edit `Modules/PublicPage/routes/web.php`

Add the import at the top of the file:

```php
use Modules\PublicPage\Http\Controllers\Admin\AdminPhotoController;
```

Add the following route groups **inside** the existing `admin` middleware group, after the `videos` prefix group:

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

## PHASE 6 — Admin UI: Photos Tab on Event Show Page

**Goal:** Add a "Photos" tab to the existing Event Show admin page. Keep Videos tab behaviour unchanged.

### Step 6.1 — Read the existing admin Event Show page

Read `Modules/PublicPage/resources/js/Pages/Admin/Events/Show.svelte` in full before editing. Note the existing tab switcher pattern (if any) and the component structure.

### Step 6.2 — Create `AdminPhotosTab.svelte` component

Create `Modules/PublicPage/resources/js/Components/Admin/AdminPhotosTab.svelte`.

The component receives props: `event` (full event object with `.photos` array) and `uploadRoute` (route string for upload POST).

Behaviour:
- Multi-file drop zone (`accept="image/*"`, `multiple`) with drag-over highlight
- Preview grid of selected files before upload (click × to remove from selection)
- Upload button → POST `FormData` to `admin.events.photos.store` using `fetch()` with CSRF, then call `router.reload({ only: ['event'] })` on success
- Existing photos grid: square thumbnails, inline alt-text input (blur → PUT to `admin.photos.update`), drag handle for reorder, delete button with confirmation
- Drag-to-reorder → POST to `admin.events.photos.reorder` on drop
- Delete → DELETE to `admin.photos.destroy`; remove from local array on success
- Empty state with "Upload First Photo" CTA if `event.photos.length === 0`
- All flash messages via `window.ToastLarge.fire(...)` matching existing admin pattern

### Step 6.3 — Update `Admin/Events/Show.svelte`

- Add `[Videos] [Photos]` tab switcher above the existing Videos section (read sibling files to match button style)
- Wrap existing Videos section in `{#if activeTab === 'videos'}…{/if}`
- Add `{#if activeTab === 'photos'}<AdminPhotosTab {event} />{/if}`
- Add `let activeTab = 'videos'` to the script section
- Update `AdminEventController@show()` to eager-load photos:

```php
$event->load([
    'videos' => fn ($q) => $q->orderBy('sort_order')->orderBy('created_at'),
    'photos' => fn ($q) => $q->orderBy('sort_order'),
]);
```

---

## PHASE 7 — Event Category Config + Seeder

**Goal:** Establish the canonical set of event categories matching the old gallery filter labels. Update validation to enforce the list.

### Step 7.1 — Create category config file

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

### Step 7.2 — Update `EventFormRequest` category validation

In `Modules/PublicPage/app/Http/Requests/Admin/EventFormRequest.php`, find the `category` rule and replace it with:

```php
'category' => ['required', 'string', \Illuminate\Validation\Rule::in(config('event_categories'))],
```

Add import at the top if not already present:

```php
use Illuminate\Validation\Rule;
```

### Step 7.3 — Update admin Create/Edit Event forms

In `Admin/Events/Create.svelte` and `Admin/Events/Edit.svelte`, change the category input from a free-text `<input>` to a `<select>` populated from a prop.

In the respective controllers (`AdminEventController@create` and `AdminEventController@edit`), pass the categories:

```php
'categories' => config('event_categories'),
```

In the Svelte pages, use:

```svelte
<select bind:value={formData.category}>
  {#each categories as cat}
    <option value={cat}>{cat}</option>
  {/each}
</select>
```

### Step 7.4 — Create `EventCategorySeeder`

```bash
vendor/bin/sail artisan make:seeder EventCategorySeeder --no-interaction
```

Edit `database/seeders/EventCategorySeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;

class EventCategorySeeder extends Seeder
{
    /**
     * Seeds one unpublished placeholder event per canonical category.
     * Useful for dev/demo. Skip if events already exist for a category.
     */
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

Register in `database/seeders/DatabaseSeeder.php` inside the `run()` method:

```php
$this->call(EventCategorySeeder::class);
```

### Step 7.5 — Run seeder (dev only)

```bash
vendor/bin/sail artisan db:seed --class=EventCategorySeeder --no-interaction
```

---

## PHASE 8 — Update Public Controller

**Goal:** Eager-load photos on the event detail route. Add `photos_count` to the media showcase index.

### Step 8.1 — Update `EventsMediaShowcaseController@show()`

In `Modules/PublicPage/app/Http/Controllers/EventsMediaShowcaseController.php`, edit `show()` to eager-load photos:

**Before:**
```php
$event->load(['videos' => ...]);
```

**After:**
```php
$event->load([
    'videos' => fn ($q) => $q->ordered(),
    'photos' => fn ($q) => $q->orderBy('sort_order'),
]);
```

Change the Inertia render call from `'PublicPage::EventVideosGrid'` to `'PublicPage::EventDetail'`.

### Step 8.2 — Update `EventsMediaShowcaseController@index()`

Add `withCount('photos')` to the query so each event has a `photos_count` attribute:

```php
Event::published()
    ->withCount(['videos', 'photos'])
    ->ordered($direction)
    ->get();
```

Pass `photos_count` through to the Inertia response. It will already be on the model as an attribute — no additional mapping needed.

---

## PHASE 9 — Public UI: EventDetail + EventPhotoGallery + EventVideoPlaylist

**Goal:** Build the new public event detail page with sequential sections: header → description → photo gallery (capped) → video playlist.

### Step 9.1 — Rename `EventVideosGrid.svelte` → `EventDetail.svelte`

```bash
mv Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte \
   Modules/PublicPage/resources/js/Pages/EventDetail.svelte
```

### Step 9.2 — Rewrite `EventDetail.svelte`

Replace the page content. Top-level layout:

1. `EventHeader` — existing component (name, date, category)
2. Description block — plain text, rendered only if `event.description` is non-empty
3. `EventPhotoGallery` — new component, rendered only if `photos.length > 0`
4. `EventVideoPlaylist` — new component, rendered only if `videos.length > 0`
5. Empty state — shown if both arrays are empty

Props passed from controller: `event`, `photos` (ordered), `videos` (ordered).

### Step 9.3 — Create `EventPhotoGallery.svelte`

Create `Modules/PublicPage/resources/js/Components/EventPhotoGallery.svelte`.

**Props:** `photos` (array of EventPhoto objects).

**Fatigue guard — preview cap:**
- Render max 12 photos in the grid
- If `photos.length > 12`: show "View all {photos.length} photos" button below grid
- Clicking any thumbnail OR the "View all" button opens the lightbox

**Grid:**
- Responsive: 4 cols desktop → 3 cols tablet → 2 cols mobile
- Square thumbnails using `thumbnail_url` (`aspect-ratio: 1`, `object-fit: cover`)
- Lazy-load via existing `LazyThumbnail.svelte` where possible; fall back to `<img loading="lazy">` if not compatible

**Lightbox modal:**
- Full-size image via `photo_url`
- `alt_text` shown as caption below image (if present)
- Photo counter: e.g. `3 / 47`
- Prev/next arrow buttons
- Keyboard nav: ← → Escape
- Click backdrop to close
- All 47 photos navigable from lightbox even though only 12 show in grid

### Step 9.4 — Create `EventVideoPlaylist.svelte`

Create `Modules/PublicPage/resources/js/Components/EventVideoPlaylist.svelte`.

**Props:** `videos` (ordered array of Video objects, already sorted by `sort_order`).

**Desktop layout (side-by-side, ~65/35 split):**
```
┌────────────────────────────────┬─────────────────────┐
│                                │  Up Next             │
│   <video> player (16:9)        │  ┌───────────────┐  │
│                                │  │▶ 1. Title 2:30│  │  ← active
│   Title of active video        │  │  2. Title 4:12│  │
│   Description (3-line clamp)   │  │  3. Title 1:45│  │
│                                │  │  4. Title 3:00│  │
│                                │  └───────────────┘  │
└────────────────────────────────┴─────────────────────┘
```

**Mobile layout (stacked):**
```
┌─────────────────────┐
│  <video> player     │
│  Title / desc       │
├─────────────────────┤
│  Up Next            │
│  ▶ 1. Title   2:30  │
│    2. Title   4:12  │
│    3. Title   1:45  │
└─────────────────────┘
```

**Behaviour:**
- `activeIndex = 0` on mount — first video loads as active
- Player uses `video.thumbnail_url` as `poster` attribute — no autoplay
- Clicking a playlist row: sets `activeIndex`, scrolls player into view on mobile (`scrollIntoView({ behavior: 'smooth' })`)
- `on:ended` on `<video>`: if `activeIndex < videos.length - 1`, increment `activeIndex`; else stop
- Active row: accent left border + bold title + background highlight
- Playlist panel: `overflow-y: auto`, `max-height` set so it aligns with player height on desktop; auto-scrolls active row into view via `scrollIntoView` inside a reactive statement
- Each playlist row: index number, `thumbnail_url` thumbnail (40×30, `object-cover`), title, `format_duration` accessor value
- Description area below player: `display: -webkit-box; -webkit-line-clamp: 3; overflow: hidden` — no expand

**Video error handling — MUST be preserved from `EventVideosGrid.svelte`:**

The existing page has retry logic that must be carried into the new playlist component verbatim. Read `EventVideosGrid.svelte` before writing `EventVideoPlaylist.svelte` and copy the following exactly:

- `MAX_RETRIES` constant and `retryCount` state variable
- `handleVideoError(event)` function — reads `video.error.code`, maps to human-readable message, increments `retryCount`, sets `videoError` state
- `handleRetry()` function — clears error state, reloads `<video>` src
- `getVideoErrorMessage(code)` function — maps error codes (2 = network, 3 = decode) to strings
- `on:error={handleVideoError}` on the `<video>` element
- Error overlay rendered when `videoError` is set: shows message + "Retry" button (only if `retryCount < MAX_RETRIES`)
- `retryCount` must reset to `0` whenever `activeIndex` changes (i.e. user picks a new video)

The retry logic matters because videos go through an async conversion pipeline (`videos:convert-pending` + `ConvertVideoToMp4` job). A video that is still converting or whose URL is temporarily unavailable will trigger a load error — the retry UI is the user's recovery path.

---

## PHASE 10 — Rewrite `/gallery` as Dynamic Photo Gallery

**Goal:** Replace the static 6-image hardcoded gallery with a paginated, category-filterable photo grid sourced from `event_photos`. Keep the `/gallery` URL — it serves visitors who want to browse all photos across events without the event-centric view of `/events/media-showcase`.

### Step 10.1 — Rewrite `gallery()` in `PublicPageController`

In `Modules/PublicPage/app/Http/Controllers/PublicPageController.php`, replace the existing `gallery()` method:

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
        'pageTitle'      => 'Photo Gallery',
        'photos'         => $photos,
        'categories'     => $categories,
        'activeCategory' => $category,
    ])->withViewData([
        'pageTitle' => 'Photo Gallery',
        'metaDesc'  => config('app.alt_name') . ' — photos from our events and activities.',
        'ogUrl'     => route('app.gallery'),
        'canonical' => route('app.gallery'),
    ]);
}
```

### Step 10.2 — Rewrite `Gallery.svelte`

Read `Modules/PublicPage/resources/js/Pages/Gallery.svelte` in full before editing. Preserve the existing outer layout structure (`.projects-grid`, `.container`, `.row` classes) — only replace the internal content.

**Category filter bar:**
- "All" link + one link per `$page.props.categories`
- Active state via existing `.active` CSS class — match the existing filter pattern already in the file
- Click → `router.get(route('app.gallery', { category }), { preserveState: true, preserveScroll: true, only: ['photos', 'activeCategory'] })`
- "All" → `router.get(route('app.gallery'), { ... })` with no `category` param

**Photo grid:**
- Preserve existing `.project-item` / `.project__img` / `.project__content` card structure
- Each card: `thumbnail_url` image, overlay link to `/events/{photo.event.slug}`, event name + category below
- `loading="lazy"` on all thumbnails
- Click thumbnail → open lightbox

**Lightbox:**
- Same keyboard nav + backdrop-close pattern as `EventPhotoGallery.svelte` (Phase 9)
- Counter: `N / total` where total is `photoList.length` (current page only)
- "View event →" link inside lightbox: `route('events.show', { slug: photo.event.slug })`
- **Note:** lightbox navigates within the current page's loaded photos only. As user loads more via "Load More", the array grows and lightbox range expands accordingly.

**"Load More" pagination:**
- Show "Load More" button when `photos.next_page_url` is non-null
- `router.get(photos.next_page_url, { preserveState: true, preserveScroll: true, only: ['photos'] })`
- **Append** loaded photos to existing `photoList` array — do not replace, so lightbox can navigate across loaded pages

**Empty state:**
- "No photos found." centred message when `photos.data.length === 0`

**Do NOT delete** the static image files in `Modules/PublicPage/resources/template/images/case-studies/grid/`. See Phase 13.

---

## PHASE 11 — Tests

**Goal:** Cover all new behaviour with PHPUnit feature tests. Do not remove any existing tests.

### Step 11.1 — Create `AdminEventPhotoTest`

```bash
vendor/bin/sail artisan make:test Admin/AdminEventPhotoTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Test cases for `Modules/PublicPage/tests/Feature/Admin/AdminEventPhotoTest.php`:

| Test | Assertion |
|------|-----------|
| Admin can upload single photo | 200 JSON, DB record created, file exists on disk |
| Admin can bulk-upload multiple photos | All records created, `sort_order` sequential |
| Non-admin cannot upload (403) | — |
| Unauthenticated cannot upload (401) | — |
| Rejects non-image file | 422 validation error |
| Rejects file over 10 MB | 422 validation error |
| Admin can update alt_text | DB field updated |
| Admin can reorder photos | `sort_order` values updated per submitted array |
| Admin can delete photo | DB record gone, both files deleted from disk |

### Step 11.2 — Create `EventDetailPageTest`

```bash
vendor/bin/sail artisan make:test EventDetailPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Test cases for `Modules/PublicPage/tests/Feature/EventDetailPageTest.php`:

| Test | Assertion |
|------|-----------|
| Published event detail returns 200 | — |
| Response includes `photos` array | Ordered by `sort_order` |
| Response includes `videos` array | Ordered by `sort_order` |
| Event with no photos returns `photos = []` | — |
| Event with no videos returns `videos = []` | — |
| Unpublished event returns 404 | — |

### Step 11.3 — Create `GalleryPageTest`

```bash
vendor/bin/sail artisan make:test GalleryPageTest --phpunit --path=Modules/PublicPage/tests --no-interaction
```

Test cases for `Modules/PublicPage/tests/Feature/GalleryPageTest.php`:

| Test | Assertion |
|------|-----------|
| GET /gallery returns 200 | — |
| Response includes `photos` paginated data | — |
| Response includes `categories` from published events with photos | — |
| Filter by category returns only matching photos | — |
| Empty state when no photos exist | `photos.data` is empty array |
| Photos from unpublished events excluded | — |

### Step 11.5 — Update `MediaShowcaseIndexTest` (if it exists)

Add test: index response includes `photos_count` attribute on each event object.

### Step 11.6 — Run tests

```bash
vendor/bin/sail artisan test --compact --filter=AdminEventPhotoTest --no-interaction
vendor/bin/sail artisan test --compact --filter=EventDetailPageTest --no-interaction
vendor/bin/sail artisan test --compact --filter=GalleryPageTest --no-interaction
```

All must pass before marking Phase 11 complete.

---

## PHASE 12 — Fix `enhanced:img` Across Svelte Files

**Goal:** Replace all `<enhanced:img>` tags with regular `<img>` + `getImgUrl()` helper. Dynamic uploaded images (photos, video thumbnails) are not affected — they already use plain `<img src={url}>`.

**Background:** `@sveltejs/enhanced-img` is a build-time optimisation that cannot process runtime/database-sourced URLs. Static template assets loaded with `?enhanced` cause build warnings and inconsistent behaviour. The fix is uniform across all affected files.

### Step 12.1 — Verify `getImgUrl` helper path

Read `Modules/PublicPage/resources/js/Pages/Layouts/PublicPageLayout.svelte` or any file already using `getImgUrl` to confirm the correct import path (likely `import { getImgUrl } from '@/helpers'` or similar).

### Step 12.2 — Fix pattern

For each file listed below:

1. Add `import { getImgUrl } from '@/helpers';` to `<script>` if not already present
2. Replace `<enhanced:img src="@publicpage-template/path/to/image.ext?enhanced"` with `<img src={getImgUrl('Modules/PublicPage/resources/template/assets/path/to/image.ext')}`
3. Remove the `?enhanced` query param
4. Keep all other attributes (`class`, `alt`, `width`, `height`, etc.) unchanged

**Files to fix:**

| File | Approx. instances |
|------|-------------------|
| `Modules/PublicPage/resources/js/Components/Header.svelte` | 2 |
| `Modules/PublicPage/resources/js/Components/Footer.svelte` | 1 |
| `Modules/PublicPage/resources/js/Partials/PageTitle.svelte` | 1 |
| `Modules/PublicPage/resources/js/Pages/BlogIndex.svelte` | 1 |
| `Modules/PublicPage/resources/js/Pages/Careers.svelte` | 2 |
| `Modules/PublicPage/resources/js/Pages/Awards.svelte` | 4 |
| `Modules/PublicPage/resources/js/Components/SummarizedProjects.svelte` | 6 |
| `Modules/PublicPage/resources/js/Components/SummarizedWhatWeDo.svelte` | 4 |
| `Modules/PublicPage/resources/js/Components/SummarizedAbout.svelte` | 2 |
| `Modules/PublicPage/resources/js/Components/OurPartners.svelte` | 6 |
| `Modules/PublicPage/resources/js/Components/CTATwo.svelte` | 1 |
| `Modules/PublicPage/resources/js/Components/Testimonials.svelte` | 1 |
| `Modules/PublicPage/resources/js/Pages/LaunchConference.svelte` | 6 |

Read each file before editing. Instance counts above are estimates — the file content is the source of truth.

### Step 12.3 — Build and verify

```bash
vendor/bin/sail bun run build
```

Build must complete with zero errors. Check output for any remaining `enhanced:img` warnings.

---

## PHASE 13 — Gallery Image Cleanup (Manual — User Action Required)

> **This phase is a manual human decision. Do not automate it.**
>
> The static placeholder images at `Modules/PublicPage/resources/template/images/case-studies/grid/` are no longer rendered anywhere now that `/gallery` redirects to the media showcase and `Gallery.svelte` has been deleted.
>
> **Before deleting anything, decide:**
>
> - **Are these real photos from actual events?**
>   → Upload them through the Admin → Events → [event] → Photos tab to the correct event. Then delete the files.
>
> - **Are these placeholder/stock images that were never real content?**
>   → Delete the directory directly — they serve no purpose.
>
> Once decided, either run the admin upload flow or:
>
> ```bash
> rm -rf Modules/PublicPage/resources/template/images/case-studies/grid/
> ```
>
> This step is intentionally left as a human judgment call and is out of scope for automated implementation.

---

## Success Criteria

| # | Criterion |
|---|-----------|
| 1 | Admin can upload photos to an event via the Photos tab on the Event Show page |
| 2 | Thumbnails (320×240) are generated server-side for every uploaded photo |
| 3 | Admin can set alt text, reorder (drag), and delete photos |
| 4 | Event categories are restricted to: Protests, Awards, Charity Drive, Elections, Free Medicals |
| 5 | Public event detail page (`/events/{slug}`) shows header → description → photo gallery → video playlist |
| 6 | Photo gallery shows max 12 by default; "View all" opens lightbox with all photos + nav |
| 7 | Video section is a playlist — inline player, auto-advance, no modal |
| 8 | `/gallery` shows dynamic paginated photo grid, filterable by event category, with lightbox |
| 9 | Media showcase index shows photo + video count badges per event |
| 10 | All `enhanced:img` tags replaced; `bun run build` completes with zero errors |
| 11 | All new tests pass; existing test suite continues to pass |
| 12 | Gallery image cleanup decision made and acted upon |

---

## Post-Implementation Notes

### URL Structure

| URL | Before | After |
|-----|--------|-------|
| `/gallery` | Static image grid (hardcoded) | Dynamic photo grid (filterable by category, paginated) |
| `/events/media-showcase` | Event cards (video count only) | Event cards (photo + video count badges) |
| `/events/{slug}` | Video-only grid + modal | EventDetail: header → photos → playlist |

### Storage Layout

```
storage/app/public/
  event-photos/
    {uuid}.{ext}            ← full-size (original upload)
    thumbnails/
      {uuid}.{ext}          ← 320×240 crop (Intervention Image)
  videos/
    …                       ← existing, unchanged
```

### Admin Workflow

| Task | Where |
|------|-------|
| Create event | Admin → Events → Create |
| Upload videos | Admin → Events → [event] → Videos tab |
| Upload photos | Admin → Events → [event] → Photos tab |
| Reorder photos | Drag handles in Photos tab |
| Set alt text | Inline input in Photos tab (saves on blur) |

### Known Deferred Work

| Item | Reason deferred |
|------|----------------|
| S3 / CDN storage | Local disk sufficient for now; migrate when traffic demands |
| WebP / AVIF conversion | Intervention Image supports it; add when needed |
| Caption (visible text below photo) | `alt_text` covers accessibility; visible caption can be added as a separate column later |
| Photo upload during event creation | Only available post-creation via Show page; add to Create form in a future PR if needed |

---

End of Plan.

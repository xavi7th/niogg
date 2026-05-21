# Phase 3: Custom Thumbnail Upload - Research

**Researched:** 2026-02-04
**Domain:** Laravel file uploads, Inertia.js with Svelte, Image validation/Storage, FFmpeg thumbnail generation
**Confidence:** HIGH

## Summary

This phase requires implementing custom thumbnail uploads for videos in a Laravel + Inertia.js + Svelte application. The research confirms that Laravel's built-in file upload handling with `Storage` facade on the `public` disk is the standard approach, paired with Form Request validation for security. Inertia.js automatically converts form data to `FormData` for multipart uploads.

**Primary recommendation:** Use Laravel's `Storage::disk('public')` with `image` validation rule (allows jpeg, png, bmp, gif, svg), store in `videos/thumbnails/custom/` folder with UUID-based filenames, and append `?v={updated_at->timestamp}` query parameter to URLs for browser cache invalidation.

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Laravel Storage | v10 | File storage via `Storage` facade | Built-in, handles local filesystem abstraction |
| Intervention Image | v3.11.6 | Image processing (already installed) | Used by existing `VideoThumbnailService` |
| Inertia.js | v1.0 | FormData conversion for multipart uploads | Automatic handling of file uploads |
| pbmedia/laravel-ffmpeg | v8.7.1 | Auto-generated thumbnails (already installed) | Existing `VideoThumbnailService` uses FFmpeg |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| None required | - | No additional packages needed | Laravel core handles all requirements |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Storage facade | Direct file manipulation | Storage provides disk abstraction, easier testing |
| `image` validation rule | `mimes:jpeg,png,...` | `image` rule is simpler and covers all browser-supported formats |
| UUID filenames | Original filenames | UUID prevents conflicts and naming issues |

**Installation:** No packages required - all dependencies already installed.

## Architecture Patterns

### Recommended Project Structure
```
Modules/PublicPage/
├── app/
│   ├── Http/
│   │   ├── Controllers/Admin/
│   │   │   └── AdminVideoController.php  # Add thumbnail upload endpoint
│   │   └── Requests/Admin/
│   │       └── VideoThumbnailRequest.php  # NEW: Form request for thumbnail validation
│   ├── Models/
│   │   └── Video.php                      # Add custom_thumbnail_url column + accessor
│   └── Services/
│       └── VideoThumbnailService.php       # Add custom thumbnail handling methods
├── database/
│   └── migrations/
│       └── xxxx_add_custom_thumbnail_to_videos_table.php  # NEW: Add custom_thumbnail_url column
└── resources/js/
    └── Components/Admin/
        └── VideoEditModal.svelte          # Add thumbnail upload section
```

### Pattern 1: Laravel File Upload with Inertia.js
**What:** Inertia automatically converts form data with files to `FormData` for multipart/form-data requests
**When to use:** Any file upload through Inertia forms
**Example:**
```javascript
// Source: https://inertiajs.com/docs/v2/the-basics/file-uploads
// Svelte component with file input

let formData = new FormData();
formData.append('thumbnail', file); // Inertia handles conversion

router.post(`/admin/videos/${video.id}/thumbnail`, formData, {
  forceFormData: true, // Ensure FormData conversion
  onProgress: (progress) => {
    uploadProgress = progress.percentage;
  }
});
```

### Pattern 2: Image Validation in Laravel
**What:** Use `image` validation rule for browser-supported image formats
**When to use:** Validating image uploads
**Example:**
```php
// Source: https://laraveldaily.com/post/four-laravel-validation-rules-for-images-and-photos
class VideoThumbnailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'thumbnail' => [
                'required',
                'image',           // Validates: jpeg, png, bmp, gif, svg
                'max:5120',        // 5MB max (in kilobytes)
                // No dimensions requirement per CONTEXT.md decision
            ],
        ];
    }
}
```

### Pattern 3: Storage on Public Disk with UUID Naming
**What:** Store files in `storage/app/public` with UUID filenames for uniqueness
**When to use:** Any file that needs public URL access
**Example:**
```php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

$extension = $file->getClientOriginalExtension();
$filename = Str::uuid()->toString() . '.' . $extension;
$path = "videos/thumbnails/custom/{$filename}";

Storage::disk('public')->put($path, file_get_contents($file));
$url = Storage::disk('public')->url($path);
```

### Pattern 4: Browser Cache Busting with Timestamp Query Parameter
**What:** Append `?v={timestamp}` to image URLs to force browser cache refresh
**When to use:** Any user-uploaded content that may change
**Example:**
```php
// In Video model accessor
public function getThumbnailUrlAttribute(): string
{
    // Custom thumbnail with cache busting
    if (!empty($this->custom_thumbnail_url)) {
        return $this->custom_thumbnail_url . '?v=' . $this->updated_at->timestamp;
    }

    // Auto-generated thumbnail
    if (!empty($this->thumbnail_url)) {
        return $this->thumbnail_url . '?v=' . $this->updated_at->timestamp;
    }

    // Fallback to placeholder
    return asset('images/video-placeholder-default.jpg');
}
```

### Anti-Patterns to Avoid
- **Storing files outside Storage facade:** Direct filesystem manipulation breaks abstraction and testing
- **Using `mimes` rule instead of `image`:** `image` rule is simpler and covers all browser formats
- **Original filenames for uploads:** Causes conflicts, security issues, and encoding problems
- **Manual MIME type checking:** Laravel's `image` rule handles this internally
- **Query string cache busting debate:** Some sources recommend against query strings, but for user-generated content with `updated_at`, it's the simplest automatic approach

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| File validation | Custom MIME checking | Laravel `image` validation rule | Handles browser formats, MIME sniffing, size limits |
| File storage | `move_uploaded_file()` | `Storage::disk('public')->put()` | Disk abstraction, easier testing, consistent API |
| Cache invalidation | Manual version strings | `?v={$model->updated_at->timestamp}` | Automatic invalidation on model update |
| Thumbnail generation | Custom image processing | Existing `VideoThumbnailService` | Already uses FFmpeg + Intervention Image |
| Form handling | Manual FormData | Inertia `router.post()` with `forceFormData` | Automatic conversion, progress tracking |

**Key insight:** Laravel's `Storage` facade and validation rules handle 90% of file upload requirements. Inertia adds automatic FormData conversion. The only custom code needed is business logic (where to store, what to name files).

## Common Pitfalls

### Pitfall 1: PUT/PATCH Requests with File Uploads
**What goes wrong:** Some servers don't support multipart/form-data on PUT/PATCH requests
**Why it happens:** CGI spec limitation, not Laravel-specific
**How to avoid:** Use POST request with `_method=PUT` for thumbnail updates (Laravel supports method spoofing)
**Warning signs:** File doesn't reach controller, empty `UploadedFile` object

### Pitfall 2: Browser Cache Serving Old Thumbnails
**What goes wrong:** User uploads new thumbnail but browser shows cached version
**Why it happens:** Browser caches images by URL, unchanged URL = cached content
**How to avoid:** Append `?v={updated_at->timestamp}` to image URLs (model automatically updates on save)
**Warning signs:** Changes not visible after upload, hard refresh required

### Pitfall 3: Orphaned Files When Replacing Thumbnails
**What goes wrong:** Old thumbnail files remain in storage after replacement
**Why it happens:** Code uploads new file without deleting old one
**How to avoid:** Always delete old file path before storing new one
**Warning signs:** Storage directory grows indefinitely

### Pitfall 4: Accidental Public Access to Private Files
**What goes wrong:** Files stored in `storage/app` instead of `storage/app/public`
**Why it happens:** Default `local` disk vs `public` disk confusion
**How to avoid:** Always use `Storage::disk('public')` for files needing public URLs
**Warning signs:** 404 errors when accessing `/storage/` URLs

### Pitfall 5: Validation Rule Too Restrictive
**What goes wrong:** Valid images rejected because extension not in allowlist
**Why it happens:** Using `mimes:jpeg,png` instead of `image` rule
**How to avoid:** Use `image` validation rule - covers jpeg, png, bmp, gif, svg
**Warning signs:** "The thumbnail must be an image" errors for valid images

## Code Examples

Verified patterns from official sources:

### Image Upload Validation
```php
// Source: https://laraveldaily.com/post/four-laravel-validation-rules-for-images-and-photos
class VideoThumbnailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'thumbnail' => ['required', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'thumbnail.required' => 'Please select an image file.',
            'thumbnail.image' => 'The file must be an image.',
            'thumbnail.max' => 'The image may not be larger than 5MB.',
        ];
    }
}
```

### Storing Uploaded File with UUID Filename
```php
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function storeCustomThumbnail(UploadedFile $file, Video $video): string
{
    // Generate unique filename preserving extension
    $extension = strtolower($file->getClientOriginalExtension());
    $filename = Str::uuid()->toString() . '.' . $extension;
    $path = "videos/thumbnails/custom/{$filename}";

    // Delete old thumbnail if exists
    if (!empty($video->custom_thumbnail_url)) {
        $oldPath = str_replace(Storage::disk('public')->url(''), '', $video->custom_thumbnail_url);
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    // Store new file
    Storage::disk('public')->put($path, file_get_contents($file));

    return Storage::disk('public')->url($path);
}
```

### Inertia File Upload with Progress (Svelte)
```javascript
// Source: https://inertiajs.com/docs/v2/the-basics/file-uploads
import { router } from '@inertiajs/svelte';

let fileInput;
let uploading = false;
let uploadProgress = 0;

async function uploadThumbnail() {
  const file = fileInput.files[0];
  if (!file) return;

  uploading = true;
  const formData = new FormData();
  formData.append('thumbnail', file);

  router.post(`/admin/videos/${video.id}/thumbnail`, formData, {
    forceFormData: true,
    onProgress: (progress) => {
      uploadProgress = progress.percentage;
    },
    onSuccess: () => {
      uploading = false;
      uploadProgress = 0;
    },
    onError: () => {
      uploading = false;
    }
  });
}
```

### Thumbnail Accessor with Fallback Chain
```php
// In Video model
protected $appends = ['thumbnail_url']; // Serialize to JSON

public function getThumbnailUrlAttribute(): string
{
    // Priority 1: Custom thumbnail (with cache busting)
    if (!empty($this->custom_thumbnail_url)) {
        // Remove existing query string before adding new one
        $url = strtok($this->custom_thumbnail_url, '?');
        return $url . '?v=' . $this->updated_at->timestamp;
    }

    // Priority 2: Auto-generated thumbnail (with cache busting)
    if (!empty($this->thumbnail_url)) {
        $url = strtok($this->thumbnail_url, '?');
        return $url . '?v=' . $this->updated_at->timestamp;
    }

    // Priority 3: Placeholder (no cache busting needed)
    return asset('images/video-placeholder-default.jpg');
}
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Manual FormData handling | Inertia automatic conversion | Inertia v1.0 | Simpler code, less boilerplate |
| Query string cache busting debate | `?v=timestamp` accepted for dynamic content | 2025+ | Widely used for user-generated content |
| `mimes:jpeg,png` validation | `image` validation rule | Laravel 5.x+ | Simpler, covers more formats |

**Deprecated/outdated:**
- **Manual file size checking:** Use `max:5120` validation rule instead
- **Custom MIME type sniffing:** `image` rule handles this internally
- **Hardcoded version strings:** Use model timestamps for automatic invalidation

## Claude's Discretion Recommendations

### File Naming Strategy (CONTEXT.md: User's Decision Required)
**Recommendation:** Use UUID-based filenames with original extension preserved

**Pros:**
- Guarantees uniqueness (no conflicts)
- No filename sanitization needed
- No encoding issues (UTF-8 filenames)
- Consistent with existing `VideoThumbnailService` pattern

**Cons:**
- Filenames not human-readable in storage
- Requires database to track which file belongs to which video

**Example:** `a1b2c3d4-e5f6-7890-abcd-ef1234567890.jpg`

### Folder Structure (CONTEXT.md: User's Decision Required)
**Recommendation:** Store in `storage/app/public/videos/thumbnails/custom/`

**Rationale:**
- Separates custom uploads from auto-generated (in `videos/thumbnails/`)
- Maintains consistency with existing storage structure
- `custom/` subfolder makes cleanup/management easier

**Structure:**
```
storage/app/public/
└── videos/
    ├── {event-slug}/           # Original video files
    ├── thumbnails/             # Auto-generated thumbnails
    │   └── {uuid}_medium.jpg
    └── thumbnails/
        └── custom/             # Custom uploaded thumbnails
            └── {uuid}.jpg
```

### Auto-generated Thumbnail Caching (CONTEXT.md: User's Decision Required)
**Recommendation:** Derive paths, don't cache in database

**Rationale:**
- Auto-generated paths follow predictable pattern: `{uuid}_medium.jpg`
- Can derive path from video's `video_url` (shared UUID base)
- No additional column needed
- Existing `VideoThumbnailService::getAllSizes()` already implements this pattern

**Implementation:**
```php
// Auto-generated paths are derived from video_url
// video_url: /storage/videos/{uuid}.mp4
// thumbnail_url: /storage/videos/thumbnails/{uuid}_medium.jpg

// Derivation logic already in VideoThumbnailService
$baseName = str_replace('.mp4', '', basename($video->video_url));
$autoThumbnail = "/storage/videos/thumbnails/{$baseName}_medium.jpg";
```

## Open Questions

None - all research domains resolved with HIGH confidence.

## Sources

### Primary (HIGH confidence)
- [Inertia.js - File Uploads](https://inertiajs.com/docs/v2/the-basics/file-uploads) - Official documentation on FormData conversion and multipart uploads
- [Laravel Daily - Four Validation Rules for Images](https://laraveldaily.com/post/four-laravel-validation-rules-for-images-and-photos) - Image validation rules (`image`, `mimes`, `size`, `dimensions`)
- [Laravel 10 - Filesystems Configuration](https://laravel.com/docs/10.x/filesystem) - Storage facade and public disk configuration (from official docs)

### Secondary (MEDIUM confidence)
- [Medium - Cache Invalidation in Laravel](https://medium.com/@developerawam/cache-invalidation-in-laravel-how-it-works-strategies-to-keep-your-cache-accurate-994029bccffd) - Cache invalidation strategies
- [Inspector - Best Practices for Cache Invalidation](https://inspector.dev/best-practices-for-cache-invalidation-in-laravel/) - Cache busting approaches
- [Stack Overflow - Cache Busting Images](https://stackoverflow.com/questions/56896730/cache-busting-images-that-are-fetched-use-query-params) - Query parameter cache busting

### Tertiary (LOW confidence)
- [Laracasts - Browser Caching for Images](https://laracasts.com/discuss/channels/requests/browser-caching-for-images) - Community discussion on image caching
- [Medium - Best Practices for File Uploads in Laravel](https://dev.to/mwacharo/best-practices-for-file-uploads-in-laravel-4791) - File upload best practices (June 2025)
- [Medium - Cache Busting with Laravel](https://medium.com/@kng_maaj/cache-bursting-how-to-solve-versioning-issues-on-laravel-app-5179fc924df5) - Versioning strategies

### Existing Codebase (Verified)
- `/Modules/PublicPage/app/Services/VideoThumbnailService.php` - FFmpeg thumbnail generation pattern
- `/Modules/PublicPage/app/Services/VideoUploadService.php` - Existing chunked upload pattern
- `/Modules/PublicPage/app/Models/Video.php` - Current Video model structure
- `/Modules/PublicPage/resources/js/Pages/Admin/Videos/Upload.svelte` - Existing file upload patterns

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - All packages verified in composer.json, Laravel 10 official docs
- Architecture: HIGH - Inertia docs verified, existing codebase patterns analyzed
- Pitfalls: HIGH - Official docs + WebSearch verified, common Laravel issues documented
- Cache busting: MEDIUM - WebSearch sources agree on query parameter approach for dynamic content

**Research date:** 2026-02-04
**Valid until:** 2026-03-06 (30 days - stable Laravel ecosystem, versions unlikely to change)

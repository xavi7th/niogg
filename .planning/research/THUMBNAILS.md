# Laravel Video Thumbnail Handling

**Research Date:** 2026-02-02

## Key Findings

### 1. Custom Thumbnail with Auto-Generation Fallback

**Accessor pattern:**

```php
public function getThumbnailUrlAttribute(): string
{
    return $this->custom_thumbnail_url
        ?: $this->thumbnail_url  // auto-generated
        ?: asset('images/video-placeholder-default.jpg');
}
```

### 2. Database Schema

```php
$table->string('thumbnail_url')->nullable();           // Auto-generated
$table->string('custom_thumbnail_url')->nullable();    // User upload
$table->boolean('has_custom_thumbnail')->default(false);
```

### 3. Storage Structure

```
storage/app/public/
├── thumbnails/           # Auto-generated (FFMpeg)
│   ├── small/
│   ├── medium/
│   └── large/
└── custom-thumbnails/    # User uploads
```

### 4. Upload Validation

Image files only: jpg, png, webp. Max 2MB recommended.

### 5. Current Codebase Uses

- `VideoThumbnailService` - handles FFMpeg extraction
- Generates 3 sizes: small (320x180), medium (640x360), large (1280x720)
- Extracts frame at 10% of video duration

---

## Action Items for This Project

1. Add `custom_thumbnail_url` column to videos table
2. Add thumbnail upload input to video edit form
3. Update Video model accessor to check custom first, then auto-generated
4. Store custom uploads in `videos/custom-thumbnails/`

---

*Sources: protonemedia/laravel-ffmpeg GitHub, Spatie MediaLibrary docs, Laracasts*

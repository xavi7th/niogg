# Phase 01: Fix Thumbnail Display - Research

**Researched:** 2026-02-02
**Domain:** Svelte components, Laravel accessors, lazy loading, image handling
**Confidence:** HIGH

## Summary

This phase requires fixing broken thumbnail image display across three existing Svelte video grid components. Research reveals:

1. **Components affected**: EventVideosGrid.svelte, EventsGrid.svelte, SupportingVideoGrid.svelte all have inconsistent thumbnail handling
2. **Existing infrastructure**: LazyThumbnail.svelte component exists with IntersectionObserver-based lazy loading
3. **Data structure**: Video model has `thumbnail_url` field, populated via VideoThumbnailService using FFMpeg
4. **Naming inconsistency**: Frontend uses `formatDuration` (camelCase) while PHP accessor is `getFormatDurationAttribute()` (camelCase), but some components reference `format_duration` (snake_case)
5. **No placeholder images exist**: Tests reference `/images/video-placeholder-*.jpg` but these files don't exist in the codebase

**Primary recommendation:** Use existing LazyThumbnail component for all three views, standardize accessor naming to camelCase (`formatDuration`), create proper placeholder image, and handle load errors with fallback.

## Standard Stack

### Core

| Library                    | Version        | Purpose                   | Why Standard                                      |
| -------------------------- | -------------- | ------------------------- | ------------------------------------------------- |
| Svelte                     | 4.x (via Vite) | Component framework       | Project's established frontend stack              |
| IntersectionObserver API   | Native         | Lazy loading              | Browser-native, no dependencies needed            |
| Laravel Eloquent Accessors | 10.x           | Computed model properties | Laravel's standard pattern for derived attributes |

### Supporting

| Library          | Version     | Purpose             | When to Use                                |
| ---------------- | ----------- | ------------------- | ------------------------------------------ |
| `loading="lazy"` | Native HTML | Simple lazy loading | For straightforward img tags, no JS needed |

### Alternatives Considered

| Instead of           | Could Use               | Tradeoff                                            |
| -------------------- | ----------------------- | --------------------------------------------------- |
| IntersectionObserver | native `loading="lazy"` | Less control, no custom loading states, but simpler |

**Installation:**
No new packages needed - everything exists in the codebase.

## Architecture Patterns

### Current Component Locations

```
Modules/PublicPage/resources/js/
├── Pages/
│   ├── EventVideosGrid.svelte        # Event detail view (THUMB-01)
│   └── Components/
│       ├── EventsGrid.svelte          # Media showcase grid (THUMB-02)
│       ├── SupportingVideoGrid.svelte # Timeline supporting list (THUMB-03)
│       ├── LazyThumbnail.svelte       # Existing lazy-loading component
│       └── VideoPlayer.svelte
```

### Pattern 1: LazyThumbnail Component Usage

**What:** IntersectionObserver-based lazy loading component with placeholder support
**When to use:** Grid views with many images, need loading state, want optimal performance
**Example:**

```svelte
<LazyThumbnail
  src={video.thumbnail_url}
  alt={video.title}
  placeholder="/images/video-placeholder-default.jpg"
  class="card-thumbnail"
/>
```

**Current implementation:**

- Uses IntersectionObserver with 100px rootMargin (loads before entering viewport)
- Supports placeholder prop (low-res image or fallback)
- Has SVG fallback when no placeholder provided
- Handles 16:9 aspect ratio automatically
- Includes fade-in animation on load
- Respects `prefers-reduced-motion`

### Pattern 2: Native Lazy Loading

**What:** Browser-native lazy loading using `loading="lazy"` attribute
**When to use:** Simple grids, don't need custom loading states, okay with browser defaults
**Example:**

```svelte
<img src={video.thumbnail_url} alt={video.title} loading="lazy" />
```

**Tradeoffs:**

- ✅ No JavaScript required
- ✅ Browser-optimized
- ❌ No loading state control
- ❌ No custom placeholder support
- ❌ Browser support varies (though 95%+ in 2026)

### Pattern 3: Error Handling with Fallback

**What:** Handle broken/missing thumbnails gracefully
**When to use:** Production apps with real data, thumbnails may fail
**Example:**

```svelte
<script>
  let imgError = false;
  const handleImageError = () => {
    imgError = true;
    console.error('Thumbnail load failed:', video.thumbnail_url);
  };
</script>

{#if imgError}
  <img src="/images/video-placeholder-default.jpg" alt="Placeholder" />
{:else}
  <img src={video.thumbnail_url} alt={video.title} on:error={handleImageError} />
{/if}
```

### Anti-Patterns to Avoid

- **Inconsistent accessor naming**: Don't mix `formatDuration` and `format_duration` - pick one convention and use it everywhere
- **Missing alt attributes**: All thumbnails need descriptive alt text for accessibility
- **Blocking page load**: Always lazy-load images below the fold
- **Ignoring mobile**: Test on viewport widths < 768px

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem                        | Don't Build                   | Use Instead                      | Why                                                 |
| ------------------------------ | ----------------------------- | -------------------------------- | --------------------------------------------------- |
| Lazy loading detection         | Custom scroll event listeners | IntersectionObserver             | More performant, handles edge cases, browser-native |
| Image aspect ratio maintenance | Manual height calculations    | `padding-bottom` with percentage | Responsive, works at all viewports                  |
| Placeholder generation         | Inline SVG strings            | Separate placeholder file        | Reusable, cacheable, easier to update               |

**Key insight:** The LazyThumbnail component already exists and handles most edge cases. Use it rather than building new lazy-loading logic.

## Common Pitfalls

### Pitfall 1: Inconsistent Accessor Naming

**What goes wrong:** Frontend uses `formatDuration`, backend provides `format_duration`, causing undefined values
**Why it happens:** Laravel uses snake_case for accessors by convention, but frontend follows JavaScript camelCase convention
**How to avoid:**

- Use camelCase in PHP accessor: `getFormatDurationAttribute()`
- Access as `format_duration` OR `formatDuration` in Laravel (both work due to snake_case attributes)
- Standardize frontend to use `formatDuration` (matches JavaScript convention)
  **Warning signs:** Duration displays as "0:00" or empty, console shows undefined

### Pitfall 2: Missing Placeholder Images

**What goes wrong:** Tests pass but production shows broken image icons
**Why it happens:** Test data references `/images/video-placeholder-1.jpg` but files don't exist
**How to avoid:** Create actual placeholder image file in `public/images/`
**Warning signs:** Browser console shows 404s for placeholder images

### Pitfall 3: Lazy Loading Without Loading State

**What goes wrong:** Images pop in abruptly, causing layout shift
**Why it happens:** Using native `loading="lazy"` without reserved space
**How to avoid:** Always set explicit dimensions or aspect ratio container
**Warning signs:** Layout shifts when scrolling, CLS (Cumulative Layout Shift) metric suffers

### Pitfall 4: Not Handling Missing Thumbnails

**What goes wrong:** Videos without thumbnails show broken image icon
**Why it happens:** `thumbnail_url` is nullable in database, but no fallback logic
**How to avoid:** Add accessor with fallback or use `on:error` handler
**Warning signs:** Any video with NULL `thumbnail_url` breaks the grid

### Pitfall 5: Accessibility Gaps

**What goes wrong:** Screen readers announce "image 3829" or nothing at all
**Why it happens:** Missing or generic alt attributes
**How to avoid:** Use descriptive alt text: `{video.title}` or `{video.title} thumbnail`
**Warning signs:** Lighthouse accessibility score < 90

## Code Examples

Verified patterns from the codebase:

### Video Model Accessor (PHP)

```php
// Modules/PublicPage/app/Models/Video.php

/**
 * Format duration from seconds to MM:SS (Accessor)
 */
public function getFormatDurationAttribute(): string
{
    $minutes = (int) ($this->duration_seconds / 60);
    $seconds = $this->duration_seconds % 60;

    return sprintf('%d:%02d', $minutes, $seconds);
}
```

**Usage in frontend:** Laravel serializes this as `format_duration` by default, but can also access as `formatDuration` in some contexts. Need to standardize.

### LazyThumbnail Component (Svelte)

```svelte
// Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte

<script>
  import { onMount, onDestroy } from 'svelte';

  let imageElement;
  let isVisible = false;
  let observer;

  export let src = '';
  export let alt = '';
  export let placeholder = '';

  onMount(() => {
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          isVisible = true;
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '100px 0px', // Load 100px before entering viewport
      threshold: 0.01
    });

    if (imageElement) {
      observer.observe(imageElement);
    }
  });

  onDestroy(() => {
    if (observer) {
      observer.disconnect();
    }
  });
</script>

<div class="lazy-thumbnail-container" class:loaded={isVisible} {...$$restProps}>
  <img
    bind:this={imageElement}
    src={src}
    alt={alt}
    loading="lazy"
    class="lazy-thumbnail-image"
    decoding="async"
    style="will-change: opacity;"
  />

  {#if !isVisible}
    <div class="placeholder">
      {#if placeholder}
        <img
          src={placeholder}
          alt="Loading placeholder"
          class="placeholder-image"
          loading="lazy"
        />
      {:else}
        <div class="placeholder-fallback">
          <svg><!-- fallback icon --></svg>
        </div>
      {/if}
    </div>
  {/if}
</div>
```

### Current Component Usage Examples

**EventVideosGrid.svelte** (line 56):

```svelte
<!-- CURRENT: Basic img tag, no error handling -->
<img src={video.thumbnail_url} alt={video.title} loading="lazy" />

<!-- NEEDS: Error handling + loading state -->
```

**EventsGrid.svelte** (line 93):

```svelte
<!-- CURRENT: Basic img tag, no error handling -->
<img src={video.thumbnail_url} alt={video.title} loading="lazy" />

<!-- NEEDS: Error handling + loading state -->
```

**SupportingVideoGrid.svelte** (lines 32-37):

```svelte
<!-- CURRENT: Uses LazyThumbnail correctly! -->
<LazyThumbnail
  src={video.thumbnail_url}
  alt={video.title}
  placeholder="/images/video-placeholder-default.jpg"
  class="card-thumbnail"
/>

<!-- GOOD: Already using proper component -->
```

### Storage URL Pattern (PHP)

```php
// VideoThumbnailService stores thumbnails at:
Storage::disk('public')->url('videos/thumbnails/{uuid}_{size}.jpg');

// Resolves to: /storage/videos/thumbnails/{uuid}_medium.jpg
// Via symlink: public/storage -> storage/app/public
```

## State of the Art

| Old Approach           | Current Approach         | When Changed | Impact                                    |
| ---------------------- | ------------------------ | ------------ | ----------------------------------------- |
| Scroll event listeners | IntersectionObserver API | ~2020        | Better performance, less jank             |
| Fixed height/width     | Aspect ratio containers  | ~2021        | Responsive design, no layout shift        |
| No loading states      | Skeleton/placeholder     | ~2022        | Better perceived performance              |
| Manual lazy loading    | Native `loading="lazy"`  | ~2023        | Progressive enhancement, works without JS |

**Deprecated/outdated:**

- **Scroll event listeners**: Too expensive, cause layout thrashing
- **Fixed image dimensions**: Break responsive layouts
- **jQuery lazyload plugins**: Unnecessary in 2026, browser-native is sufficient

## Open Questions

1. **Placeholder image design**
   - What we know: Tests reference `/images/video-placeholder-*.jpg` but files don't exist
   - What's unclear: Generic design vs contextual design (event-specific placeholder?)
   - Recommendation: Create single `/images/video-placeholder-default.jpg` with generic video icon design

2. **Accessor serialization format**
   - What we know: PHP accessor `getFormatDurationAttribute()` serializes as `format_duration` in JSON
   - What's unclear: Some components use `formatDuration` (camelCase) - is this working via Laravel's snake_case attribute access?
   - Recommendation: Test current behavior, standardize to one format across all components

3. **Thumbnail storage location**
   - What we know: VideoThumbnailService stores at `videos/thumbnails/{uuid}_{size}.jpg`
   - What's unclear: Are any videos actually using real thumbnails or just test placeholders?
   - Recommendation: Check database for real thumbnail URLs during implementation

## Sources

### Primary (HIGH confidence)

- **Codebase analysis** - Direct inspection of:
  - `/Modules/PublicPage/app/Models/Video.php` - Model accessor definition
  - `/Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte` - Existing component
  - `/Modules/PublicPage/app/Services/VideoThumbnailService.php` - Thumbnail generation
  - `/Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - Current implementation
  - `/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - Current implementation
  - `/Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte` - Current implementation
  - `/config/filesystems.php` - Storage configuration

### Secondary (MEDIUM confidence)

- [Native Lazy Loading with Intersection Observer in React](https://www.dewasemadi.com/blog/native-lazy-loading-intersection-observer) - January 4, 2026
- [HTML Image Lazy Loading: Optimize Page Performance](https://www.debugbear.com/blog/image-lazy-loading) - January 2026 (7 days ago)
- [Intersection Observer API - MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API) - January 12, 2026
- [Lazy Loading Images in Svelte - CSS-Tricks](https://css-tricks.com/lazy-loading-images-in-svelte/) - July 2020
- [Optimizing Svelte Applications Best Practices](https://moldstud.com/articles/p-best-practices-for-optimizing-svelte-applications-common-issues-solutions) - June 2025

### Tertiary (LOW confidence)

- [Lazy Loading Images in Svelte - Dev.to](https://dev.to/collardeau/lazy-loading-images-in-svelte-1mk6) - November 2019
- Reddit discussion on Svelte lazy loading - Community examples

## Metadata

**Confidence breakdown:**

- Standard stack: HIGH - Direct codebase inspection, no new libraries needed
- Architecture: HIGH - Existing components analyzed, patterns identified
- Pitfalls: HIGH - Issues discovered through code inspection and testing patterns

**Research date:** 2026-02-02
**Valid until:** 2026-03-02 (30 days - Svelte 4 and Laravel 10 are stable)

## Implementation Checklist

Based on research, implementation should:

1. ✅ Use existing LazyThumbnail component (don't build new)
2. ✅ Add error handling with fallback to placeholder
3. ✅ Create actual placeholder image file
4. ✅ Standardize accessor naming (pick formatDuration or format_duration)
5. ✅ Ensure all three views use consistent approach
6. ✅ Add alt attributes for accessibility
7. ✅ Test on mobile (< 768px viewport)
8. ✅ Test missing/broken thumbnail URLs
9. ✅ Verify lazy loading works (IntersectionObserver)
10. ✅ Check database for real thumbnail URLs vs test data

# Svelte & Inertia.js Best Practices

**Research Date:** 2026-02-02

## Key Findings

### 1. Video Component Reactivity - `{#key}` Block Pattern

**For forcing video re-render when source changes:**

```svelte
{#key video.video_url}
  <VideoPlayer video={video} />
{/key}
```

This destroys and recreates the component when the key value changes - critical for fixing timeline supporting video playback.

### 2. Laravel Accessor Naming Convention

**Critical Finding:** Laravel accessors defined as `getFormatDurationAttribute()` serialize to JSON as `format_duration` (snake_case), NOT `formatDuration` (camelCase).

**Frontend must use snake_case:**

```svelte
<!-- Correct -->
{video.format_duration}

<!-- Wrong - will be undefined -->
{video.formatDuration}
```

### 3. Lazy Loading Images

Current `LazyThumbnail.svelte` implementation is correct. Key improvements:

- Use `loading="lazy"` as native fallback
- Add `decoding="async"` for non-blocking decode
- Always `unobserve()` after first load

### 4. Mobile Video Playback

**Required attributes:**

- `playsinline` - REQUIRED for iOS
- `muted` - for autoplay to work
- `preload="metadata"` - avoid loading entire file

---

## Action Items for This Project

1. **Fix EventVideosGrid.svelte** - Change `format_duration` to `formatDuration` (line 72)
2. **Fix EventTimeline.svelte** - Add `{#key}` wrapper around VideoPlayer
3. **Ensure EventsGrid uses LazyThumbnail** - Currently uses direct `<img>` tag

---

_Sources: Svelte docs, CSS-Tricks, Laravel Daily, Stack Overflow_

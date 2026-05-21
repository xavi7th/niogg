# Research Summary

**Research Date:** 2026-02-02

## Overview

Research covered 4 key areas for fixing video display/playback issues in Laravel + Svelte modular monolith.

## Key Findings

### Svelte/Inertia.js

- **`{#key}` blocks** force component re-render when video source changes - critical for timeline playback
- **Laravel accessor naming**: `getFormatDurationAttribute()` serializes as `format_duration` (snake_case), NOT camelCase
- **LazyThumbnail component** is correctly implemented with Intersection Observer

### Custom Thumbnails

- Add `custom_thumbnail_url` column alongside existing `thumbnail_url`
- Accessor pattern: custom → auto-generated → placeholder
- Store in separate directory: `videos/custom-thumbnails/`

### Sorting by Event Date

- Use URL query params (`?sort=newest|oldest`) for state persistence
- Update `Event::scopeOrdered($direction)` to accept direction parameter
- Use `router.get()` with `replace: true` for sort toggles

### Video Playback Issues

- **`playsinline` attribute** REQUIRED for iOS
- Modal videos need timing (setTimeout or event-based play())
- Handle play() Promise rejection for autoplay policies

## Actionable Insights

| Issue                         | Root Cause                              | Fix                         |
| ----------------------------- | --------------------------------------- | --------------------------- |
| EventVideosGrid no thumbnails | `format_duration` vs `formatDuration`   | Use snake_case in frontend  |
| Timeline videos don't play    | No re-render on video change            | Add `{#key}` wrapper        |
| Grid view no thumbnails       | Direct `<img>` instead of LazyThumbnail | Use LazyThumbnail component |
| Modal videos don't play       | Timing issue                            | setTimeout or canplay event |

## Implementation Order

1. Fix accessor naming (quick win)
2. Add `{#key}` for timeline playback
3. Replace direct `<img>` with LazyThumbnail
4. Add custom thumbnail upload feature
5. Add event date sorting

---

**Research files:**

- `SVELTE-INERTIA.md` - Svelte reactivity, accessor naming, lazy loading
- `THUMBNAILS.md` - Custom uploads, storage, schema
- `SORTING.md` - URL-based sorting, dynamic scopes
- `VIDEO-PLAYBACK.md` - Modal playback, mobile requirements, autoplay policies

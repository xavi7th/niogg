# Phase 01: Fix Thumbnail Display - Planning Summary

**Status:** Ready for execution
**Planned:** 2026-02-02
**Plans:** 3 (2 Wave 1 parallel, 1 Wave 2 sequential)

## Overview

Fix broken thumbnail display across three existing video grid components by standardizing accessor naming, adding error handling, and implementing consistent lazy loading.

## Plans by Wave

### Wave 1 (Parallel Execution)

- **01-01-fix-accessor-naming.md**: Fix `formatDuration` → `format_duration` in EventsGrid and SupportingVideoGrid
- **01-02-placeholder-and-lazythumbnail-error-handling.md**: Create placeholder image + enhance LazyThumbnail with onerror handler

### Wave 2 (Sequential, depends on Wave 1)

- **01-03-add-lazythumbnail-to-grid-views.md**: Replace `<img>` tags with LazyThumbnail in EventVideosGrid and EventsGrid

## Component Analysis

| Component                  | Current State                                                  | Required Changes                   | Plan         |
| -------------------------- | -------------------------------------------------------------- | ---------------------------------- | ------------ |
| EventVideosGrid.svelte     | Basic `<img>` tag, no error handling, uses `format_duration` ✓ | Add LazyThumbnail + error handling | 01-03        |
| EventsGrid.svelte          | Basic `<img>` tag, no error handling, uses `formatDuration` ✗  | Fix accessor + add LazyThumbnail   | 01-01, 01-03 |
| SupportingVideoGrid.svelte | Already uses LazyThumbnail ✓, uses `formatDuration` ✗          | Fix accessor only                  | 01-01        |

## Technical Decisions

1. **Accessor Naming**: Standardize to `format_duration` (snake_case) to match Laravel's serialization convention
2. **Component Approach**: Use existing LazyThumbnail component for all three views (already implemented correctly in SupportingVideoGrid)
3. **Error Handling**: Enhance LazyThumbnail with `on:error` handler to show placeholder on load failure
4. **Placeholder Image**: Create `/public/images/video-placeholder-default.jpg` as fallback

## Files Modified

1. `/Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte`
2. `/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte`
3. `/Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte`
4. `/Modules/PublicPage/resources/js/Pages/Components/LazyThumbnail.svelte`
5. `/public/images/video-placeholder-default.jpg` (NEW)

## Success Criteria

- [ ] EventVideosGrid shows thumbnails with lazy loading and error handling (THUMB-01)
- [ ] EventsGrid shows thumbnails with lazy loading and error handling (THUMB-02)
- [ ] SupportingVideoGrid shows thumbnails with correct accessor (THUMB-03)
- [ ] All views use consistent `format_duration` accessor
- [ ] Placeholder image displays for broken/missing thumbnails
- [ ] Mobile responsive (< 768px viewport)

## Unresolved Questions

None - planning complete.

---
phase: 03-custom-thumbnail-upload
plan: 01
subsystem: database
tags: [laravel, eloquent, accessor, migration, thumbnail]

# Dependency graph
requires:
  - phase: 02-fix-video-playback
    provides: video playback functionality, Video model
provides:
  - Database column for custom thumbnail storage
  - Accessor with fallback chain (custom -> auto-generated -> placeholder)
  - Cache-busting query parameter for dynamic thumbnails
affects: [03-02, 03-03]

# Tech tracking
tech-stack:
  added: []
  patterns:
    - Laravel accessor with $appends for JSON serialization
    - Fallback chain pattern for thumbnail selection
    - Cache-busting via updated_at timestamp

key-files:
  created: [Modules/PublicPage/database/migrations/2026_02_04_144014_add_custom_thumbnail_url_to_videos_table.php]
  modified: [Modules/PublicPage/app/Models/Video.php]

key-decisions:
  - "Use $appends to serialize accessor to JSON (snake_case naming follows existing pattern)"
  - "Cache-busting uses updated_at timestamp (strips existing query strings with strtok)"
  - "Placeholder has no cache busting (static asset)"

patterns-established:
  - Accessor Pattern: getThumbnailUrlAttribute returns fallback chain with priority
  - Cache Busting: strtok($url, '?') . '?v=' . timestamp strips existing QS

# Metrics
duration: 1min
completed: 2026-02-04
---

# Phase 3 Plan 1: Database and Model Support Summary

**custom_thumbnail_url database column with accessor fallback chain (custom -> auto-generated -> placeholder) using Laravel $appends for JSON serialization**

## Performance

- **Duration:** 1 min
- **Started:** 2026-02-04T14:39:59Z
- **Completed:** 2026-02-04T14:41:18Z
- **Tasks:** 3
- **Files modified:** 2

## Accomplishments

- Database migration adding `custom_thumbnail_url` column after `thumbnail_url`
- Video model updated with custom_thumbnail_url in fillable array
- Accessor `getThumbnailUrlAttribute()` with three-tier fallback chain
- Cache-busting (?v={timestamp}) applied to dynamic thumbnails
- Followed existing PHP accessor naming pattern (snake_case for JSON)

## Task Commits

Each task was committed atomically:

1. **Task 1: Create migration for custom_thumbnail_url column** - `e418a9b` (feat)
2. **Task 2: Run migration to add column** - (already in Task 1 commit)
3. **Task 3: Update Video model with custom_thumbnail_url support** - `092bd6a` (feat)

**Plan metadata:** (none - summary-only)

## Files Created/Modified

- `Modules/PublicPage/database/migrations/2026_02_04_144014_add_custom_thumbnail_url_to_videos_table.php` - Adds custom_thumbnail_url string column (nullable, after thumbnail_url)
- `Modules/PublicPage/app/Models/Video.php` - Added custom_thumbnail_url to fillable, thumbnail_url to appends, and getThumbnailUrlAttribute() accessor

## Decisions Made

- **$appends for JSON serialization**: Added `thumbnail_url` to $appends array so accessor serializes to JSON
- **Cache-busting approach**: Use `strtok($url, '?')` to strip existing query strings before appending `?v={timestamp}`
- **Placeholder handling**: Static `/images/video-placeholder-default.jpg` has no cache busting

## Deviations from Plan

None - plan executed exactly as written.

## Issues Encountered

None

## User Setup Required

None - no external service configuration required.

## Next Phase Readiness

Database and model layer complete. Ready for Plan 03-02 (admin upload UI).

---

*Phase: 03-custom-thumbnail-upload*
*Plan: 01*
*Completed: 2026-02-04*

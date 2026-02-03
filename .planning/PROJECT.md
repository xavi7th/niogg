# Video Display & Playback Fixes

## What This Is

Fix broken video thumbnails, playback issues, and add sorting/thumbnail upload features to the existing Laravel modular monolith event media showcase system. The site displays event videos with timeline and grid views, but thumbnails aren't showing and videos don't play when clicked.

## Core Value

**Users can view event videos with working thumbnails and playback.** If thumbnails or playback fail, the media showcase is non-functional.

## Requirements

### Validated

<!-- Shipped and confirmed valuable. -->

- ✓ Event management with videos — existing
- ✓ Video upload with chunked uploads (10MB chunks, 1GB max) — existing
- ✓ Auto-generated video thumbnails (FFMpeg, 3 sizes) — existing
- ✓ Video conversion to MP4 (queued jobs) — existing
- ✓ Event media showcase page with timeline and grid views — existing
- ✓ Featured video selection per event — existing
- ✓ Event ordering by date (DESC) — existing

### Active

<!-- Current scope. Building toward these. -->

- [ ] **THUMB-01**: Thumbnails display in all video views (event grid, timeline, media showcase grid)
- [ ] **THUMB-02**: Videos play when clicked in modal (event view, grid view)
- [ ] **THUMB-03**: Videos play when clicked in timeline supporting video list (desktop)
- [ ] **THUMB-04**: Video titles display correctly in timeline supporting video list
- [ ] **CUSTOM-01**: Admin can upload custom thumbnail on video edit page
- [ ] **CUSTOM-02**: System auto-generates thumbnail when no custom thumbnail provided
- [ ] **SORT-01**: Global sort toggle for events (newest/oldest by event date)
- [ ] **MOBILE-01**: Timeline view on mobile shows featured video only with button to grid

### Out of Scope

<!-- Explicit boundaries. Includes reasoning to prevent re-adding. -->

- Custom thumbnail upload on video create form — keep simple, edit-only for v1
- Video editing beyond thumbnail upload — existing edit page sufficient
- Bulk thumbnail upload — single upload per video is adequate
- Sorting by video duration/title — date-based sorting meets current need
- Pagination/infinite scroll for grid view — current page-based navigation works

## Context

**Existing System:**

- Laravel 10 modular monolith with Nwidart Modules
- Conference module manages events and videos
- PublicPage module handles public-facing media showcase
- Inertia.js + Svelte for frontend
- Video processing via FFMpeg (queued jobs)
- Chunked uploads with thread-safe progress tracking
- Auto-generated thumbnails: small (320x180), medium (640x360), large (1280x720)

**Known Issues (from CONCERNS.md):**

- Mobile video playback was recently fixed (commit 621f5c3)
- VideoThumbnailService uses FFMpeg + Intervention Image
- SupportingVideoGrid component has proper structure but may have data passing issues
- EventsGrid doesn't use LazyThumbnail component (uses direct img tag)
- Video model has `formatDuration` accessor but frontend uses `format_duration` (snake_case)

**Tech Stack:**

- PHP 8.1+, Laravel 10, MariaDB 10, Redis
- Svelte, Inertia.js, Vite, Tailwind CSS
- FFMpeg for video processing
- Docker/Laravel Sail for development

## Constraints

- **Tech Stack**: Laravel 10, Svelte, Inertia.js — must maintain existing patterns
- **Module Structure**: Nwidart Modules — keep Conference/PublicPage module separation
- **Storage**: Public disk for thumbnails — maintain existing storage structure
- **Video Processing**: Queued FFMpeg jobs — don't break async conversion

## Key Decisions

<!-- Decisions that constrain future work. Add throughout project lifecycle. -->

| Decision                            | Rationale                                                 | Outcome   |
| ----------------------------------- | --------------------------------------------------------- | --------- |
| Custom thumbnails on edit page only | Simpler v1, reduce upload form complexity                 | — Pending |
| Auto-generate as fallback           | Always have a thumbnail, even if user doesn't provide one | — Pending |
| Global sort control                 | Consistent sorting across timeline and grid views         | — Pending |
| Keep mobile timeline behavior       | Mobile UX already works (featured → grid button)          | — Pending |

---

_Last updated: 2026-02-02 after initialization_

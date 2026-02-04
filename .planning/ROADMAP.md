# Roadmap: Video Display & Playback Fixes

## Overview

Fix broken video thumbnails and playback in the existing event media showcase system, then add custom thumbnail uploads, event sorting, and mobile UX improvements. Each phase delivers a complete, verifiable capability building from core fixes to enhanced features.

## Phases

**Phase Numbering:**

- Integer phases (1, 2, 3): Planned milestone work
- Decimal phases (2.1, 2.2): Urgent insertions (marked with INSERTED)

Decimal phases appear between their surrounding integers in numeric order.

- [x] **Phase 1: Fix Thumbnail Display** - Restore thumbnail images across all video views (Completed 2026-02-03)
- [x] **Phase 2: Fix Video Playback** - Restore video playback functionality across all views (Completed 2026-02-03)
- [x] **Phase 3: Custom Thumbnail Upload** - Enable admins to upload custom thumbnails (Completed 2026-02-04)
- [x] **Phase 4: Event Sorting** - Add global sort control for event date ordering (Completed 2026-02-04)
- [ ] **Phase 5: Mobile UX** - Ensure mobile timeline and playback work correctly

## Phase Details

### Phase 1: Fix Thumbnail Display

**Goal**: Thumbnails display correctly in all video views (event grid, media showcase grid, timeline)
**Depends on**: Nothing (first phase)
**Requirements**: THUMB-01, THUMB-02, THUMB-03
**Success Criteria** (what must be TRUE):

1. Event grid view shows thumbnail images for all videos
2. Media showcase grid view shows thumbnail images for all videos
3. Timeline supporting video list shows thumbnail images for all videos
   **Plans**: 3 planned (see `.planning/phases/01-fix-thumbnail-display/plans/`)

Plans:

- [x] 01-01: Fix accessor naming (format_duration vs formatDuration) - Wave 1
- [x] 01-02: Create placeholder image and enhance LazyThumbnail error handling - Wave 1
- [x] 01-03: Add LazyThumbnail to EventVideosGrid and EventsGrid - Wave 2 (depends on 01-02)

### Phase 2: Fix Video Playback

**Goal**: Videos play when clicked in all views (modals and timeline)
**Depends on**: Phase 1
**Requirements**: THUMB-04, THUMB-05, THUMB-06, THUMB-07
**Success Criteria** (what must be TRUE):

1. Clicking video in event view modal plays the video
2. Clicking video in media showcase grid modal plays the video
3. Clicking video in timeline supporting video list plays the video
4. Video titles display correctly in timeline supporting video list
   **Plans**: 3 planned (see `.planning/phases/02-fix-video-playback/plans/`)

Plans:

- [x] 02-01: Timeline reactivity with {#key} blocks - Wave 1
- [x] 02-02: Modal autoplay with canplay event - Wave 2
- [x] 02-03: Mobile compatibility and error handling - Wave 3 (depends on 02-02)

### Phase 3: Custom Thumbnail Upload

**Goal**: Admins can upload custom thumbnails, auto-generated as fallback
**Depends on**: Phase 2
**Requirements**: CUSTOM-01, CUSTOM-02, CUSTOM-03
**Success Criteria** (what must be TRUE):

1. Admin can upload custom thumbnail image on video edit page
2. System auto-generates thumbnail when no custom thumbnail provided
3. Custom thumbnails display correctly in all video views
   **Plans**: 3 planned (see `.planning/phases/03-custom-thumbnail-upload/plans/`)

Plans:

- [x] 03-01: Add database column and model accessor with fallback chain - Wave 1
- [x] 03-02: Create backend upload handler with validation and storage - Wave 2 (depends on 03-01)
- [x] 03-03: Add admin upload UI to VideoEditModal - Wave 3 (depends on 03-02)

### Phase 4: Event Sorting

**Goal**: Users can toggle event sort order with persistent preference
**Depends on**: Phase 3
**Requirements**: SORT-01, SORT-02, SORT-03
**Success Criteria** (what must be TRUE):

1. User can toggle between newest-first and oldest-first sorting
2. Sort preference persists across page navigation via URL query param
3. Sort control applies to both timeline and grid views
   **Plans**: 3 planned (see `.planning/phases/04-event-sorting/plans/`)

Plans:

- [x] 04-01: Modify Event::ordered() scope to accept direction parameter - Wave 1
- [x] 04-02: Add sort parameter handling to EventsMediaShowcaseController - Wave 2 (depends on 04-01)
- [x] 04-03: Add sort toggle controls to EventTimeline and EventsGrid - Wave 3 (depends on 04-02)

### Phase 5: Mobile UX

**Goal**: Mobile timeline shows featured video only with correct playback
**Depends on**: Phase 4
**Requirements**: MOBILE-01, MOBILE-02, MOBILE-03
**Success Criteria** (what must be TRUE):

1. Timeline view on mobile shows featured video only
2. "Show all videos from this event" button navigates to filtered grid view
3. Videos play correctly on mobile devices (iOS/Android)
   **Plans**: TBD

Plans:

- [ ] 05-01: Verify mobile timeline layout (featured only)
- [ ] 05-02: Add navigation button to filtered grid view
- [ ] 05-03: Test playback on iOS and Android devices

## Progress

**Execution Order:**
Phases execute in numeric order: 1 -> 2 -> 3 -> 4 -> 5

| Phase                      | Plans Complete | Status      | Completed    |
| -------------------------- | -------------- | ----------- | ------------ |
| 1. Fix Thumbnail Display   | 3/3            | Verified    | 2026-02-03   |
| 2. Fix Video Playback      | 3/3            | Verified    | 2026-02-03   |
| 3. Custom Thumbnail Upload | 3/3            | Verified    | 2026-02-04   |
| 4. Event Sorting           | 3/3            | Verified    | 2026-02-04   |
| 5. Mobile UX               | 0/3            | Not started | -            |

# Requirements: Video Display & Playback Fixes

**Defined:** 2026-02-02
**Core Value:** Users can view event videos with working thumbnails and playback

## v1 Requirements

### Thumbnails

- [ ] **THUMB-01**: Videos display thumbnails in event grid view (EventVideosGrid.svelte)
- [ ] **THUMB-02**: Videos display thumbnails in media showcase grid view (EventsGrid.svelte)
- [ ] **THUMB-03**: Videos display thumbnails in timeline supporting video list (SupportingVideoGrid.svelte)
- [ ] **THUMB-04**: Videos play when clicked in event view modal (EventVideosGrid.svelte)
- [ ] **THUMB-05**: Videos play when clicked in grid view modal (EventsGrid.svelte)
- [ ] **THUMB-06**: Videos play when clicked in timeline supporting video list (EventTimeline.svelte)
- [ ] **THUMB-07**: Video titles display correctly in timeline supporting video list

### Custom Uploads

- [ ] **CUSTOM-01**: Admin can upload custom thumbnail image on video edit page
- [ ] **CUSTOM-02**: System auto-generates thumbnail when no custom thumbnail provided
- [ ] **CUSTOM-03**: Custom thumbnail displays correctly in all video views

### Sorting

- [ ] **SORT-01**: Users can toggle event sort order (newest first / oldest first)
- [ ] **SORT-02**: Sort preference persists across page navigation via URL query param
- [ ] **SORT-03**: Sort control applies to both timeline and grid views

### Mobile

- [ ] **MOBILE-01**: Timeline view on mobile shows featured video only
- [ ] **MOBILE-02**: "Show all videos from this event" button navigates to filtered grid view
- [ ] **MOBILE-03**: Videos play correctly on mobile devices (iOS/Android)

## v2 Requirements

Deferred to future release.

- **ENHANCE-01**: Bulk thumbnail upload for multiple videos
- **ENHANCE-02**: Thumbnail cropping/editing tool
- **ENHANCE-03**: Sort by video duration or title
- **ENHANCE-04**: Video editing beyond thumbnail upload

## Out of Scope

Explicitly excluded.

| Feature | Reason |
|---------|--------|
| Custom thumbnail on create form | Keep upload form simple, edit-only for v1 |
| Pagination/infinite scroll for grid | Current page-based navigation works |
| Multi-column sort | Single date-based sort meets current need |
| CDN integration | Local storage sufficient for v1 |

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| THUMB-01 | Phase 1 | Pending |
| THUMB-02 | Phase 1 | Pending |
| THUMB-03 | Phase 1 | Pending |
| THUMB-04 | Phase 2 | Pending |
| THUMB-05 | Phase 2 | Pending |
| THUMB-06 | Phase 2 | Pending |
| THUMB-07 | Phase 2 | Pending |
| CUSTOM-01 | Phase 3 | Pending |
| CUSTOM-02 | Phase 3 | Pending |
| CUSTOM-03 | Phase 3 | Pending |
| SORT-01 | Phase 4 | Pending |
| SORT-02 | Phase 4 | Pending |
| SORT-03 | Phase 4 | Pending |
| MOBILE-01 | Phase 5 | Pending |
| MOBILE-02 | Phase 5 | Pending |
| MOBILE-03 | Phase 5 | Pending |

**Coverage:**
- v1 requirements: 15 total
- Mapped to phases: 15
- Unmapped: 0

---
*Requirements defined: 2026-02-02*
*Last updated: 2026-02-02 after roadmap creation*

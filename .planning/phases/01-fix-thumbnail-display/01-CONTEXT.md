# Phase 1: Fix Thumbnail Display - Context

**Gathered:** 2026-02-02
**Status:** Ready for planning

## Phase Boundary

Restore thumbnail image display across three existing video views: event grid view, media showcase grid view, and timeline supporting video list. This phase fixes broken thumbnails, not new features like custom uploads (Phase 3).

## Implementation Decisions

### Component approach
- Each view can use the approach that makes sense for it (LazyThumbnail where appropriate, img tags where simpler)
- All thumbnails should be lazy-loaded for performance
- Add alt attributes for accessibility and SEO
- Claude's discretion: Loading state indicator design (skeleton vs blank space)

### Fallback behavior
- Show a placeholder image when thumbnail fails to load or is missing
- Claude's discretion: Placeholder design (generic vs contextual)
- No retry on load failure — show placeholder immediately
- Log thumbnail load errors for debugging

### Verification scope
- Comprehensive manual testing: verify all three views + edge cases (missing thumbs, broken URLs)
- Test on both desktop and mobile viewport sizes
- Claude's discretion: Whether to create sample test data or use existing event data

### Accessor naming convention
- Keep formatDuration (JS) and format_duration (PHP) as-is — each language follows its convention
- Audit all video model accessors for PHP snake_case consistency while fixing this
- Update any frontend code that calls the accessor incorrectly

## Specific Ideas

No specific requirements — open to standard approaches for thumbnail display and lazy loading.

## Deferred Ideas

None — discussion stayed within phase scope.

---

*Phase: 01-fix-thumbnail-display*
*Context gathered: 2026-02-02*

# Phase 3: Custom Thumbnail Upload - Context

**Gathered:** 2026-02-04
**Status:** Ready for planning

<domain>
## Phase Boundary

Admins can upload custom thumbnails for videos through the video edit page. When no custom thumbnail exists, the system auto-generates one from the video. If both fail, a placeholder image is used. Custom thumbnails display in all video views (event grid, media showcase, timeline).

</domain>

<decisions>
## Implementation Decisions

### Admin Upload UI
- Dedicated upload section on video edit page (not inline on thumbnail)
- Click-to-select file input only — no drag-and-drop
- After selection: show thumbnail preview + file name/size details
- During upload: full loading state replaces upload area until complete
- No remove button — custom thumbnails can only be replaced, not removed

### Image Validation
- Allow all browser-supported image formats (not limited to JPG/PNG/WebP)
- Maximum file size: 5MB
- No dimension or aspect ratio requirements — any dimensions accepted
- Validation errors display inline near the upload control

### Storage Handling
- **Claude's Discretion:** File naming strategy
- **Claude's Discretion:** Folder structure organization
- Delete old thumbnail file immediately when replaced (no accumulation)
- Store on local disk (storage/app/public), not cloud storage

### Fallback Behavior
- Generate auto thumbnails on video save (synchronous, not background/on-demand)
- Use existing ImageMagick placeholder from Phase 1 as final fallback
- Always invalidate browser cache when custom thumbnail changes
- **Claude's Discretion:** Whether auto-generated thumbnail paths are cached in DB or derived

</decisions>

<specifics>
## Specific Ideas

No specific requirements — open to standard approaches.

</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.

</deferred>

---

*Phase: 03-custom-thumbnail-upload*
*Context gathered: 2026-02-04*

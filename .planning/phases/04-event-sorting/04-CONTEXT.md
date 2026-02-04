# Phase 4: Event Sorting - Context

**Gathered:** 2026-02-04
**Status:** Ready for planning

<domain>
## Phase Boundary

Users can toggle event sort order between newest-first and oldest-first. Sort preference persists via URL query parameter (?sort=newest|oldest). Toggle control appears on each affected page (not global header). Default sort order is newest-first.
</domain>

<decisions>
## Implementation Decisions

### UI Control Placement
- **Page-level control** — separate toggle on each page (timeline, grid views)
- NOT in global header/navigation
- More context-aware placement within each page's layout

### Default Sort Order
- **Newest first** is the default
- Most recent events appear at the top
- Common pattern for event showcase sites

### Persistence Strategy
- **URL query parameter only** (?sort=newest|oldest)
- Shareable links maintain sort preference
- No localStorage or session storage
- No backend user preferences storage

</decisions>

<specifics>
## Specific Ideas

None — open to standard approaches for URL-based sort parameters with Inertia.js.
</specifics>

<deferred>
## Deferred Ideas

None — discussion stayed within phase scope.
</deferred>

---

*Phase: 04-event-sorting*
*Context gathered: 2026-02-04*

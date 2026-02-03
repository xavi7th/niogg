---
phase: 02-fix-video-playback
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte
autonomous: true

must_haves:
  truths:
    - "Clicking video in timeline supporting video list plays the correct video"
    - "Video player updates when switching between events in timeline"
    - "Video player clears when switching to event without featured video"
  artifacts:
    - path: "Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte"
      provides: "Timeline view with reactive video player"
      contains: "{#key}"
  key_links:
    - from: "EventTimeline.svelte"
      to: "VideoPlayer.svelte"
      via: "{#key} block on video.id"
      pattern: "{#key.*video.*\\.id"
---

<objective>
Fix timeline video player reactivity by adding {#key} block to force VideoPlayer recreation when selected video changes.

**Purpose:** When users click videos in the timeline's supporting video grid, the featured video player must update to show the new video. Current implementation may reuse the same video element, causing stale content and memory leaks.

**Output:** EventTimeline.svelte with {#key} block wrapping VideoPlayer, ensuring clean re-rendering on video changes.
</objective>

<execution_context>
@/Users/leinad/.claude/get-shit-done/workflows/execute-plan.md
@/Users/leinad/.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/phases/02-fix-video-playback/02-RESEARCH.md
@.planning/phases/01-fix-thumbnail-display/PHASE-SUMMARY.md
@/planning/PROJECT.md
</context>

<tasks>

<task type="auto">
  <name>Add {#key} block to VideoPlayer in EventTimeline</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte</files>
  <action>
In EventTimeline.svelte, wrap the VideoPlayer component with a {#key} block using the selected video's ID as the key:

1. Locate the VideoPlayer component inside the featured-player-section div (around line 42)
2. Wrap it with: `{#key getSelectedVideo(event.id)?.id || 'empty'}`
3. This forces complete VideoPlayer recreation when the selected video changes, preventing memory leaks

**Pattern to follow:**
```svelte
{#key getSelectedVideo(event.id)?.id || 'empty'}
  <VideoPlayer video={getSelectedVideo(event.id)} size="large" />
{/key}
```

**Why:** The {#key} block ensures Svelte destroys and recreates the VideoPlayer when the video ID changes, rather than reusing the same DOM element. This prevents:
- Stale video content showing when switching videos
- Memory leaks from cached video resources
- Incorrect poster images

Do NOT modify any other parts of EventTimeline.svelte - the getSelectedVideo function and handleVideoSelect logic work correctly.
</action>
  <verify>
1. Search for `{#key` in EventTimeline.svelte - should find exactly 1 match
2. Run `grep -A2 "{#key" Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte` to verify the key expression
3. Confirm the key uses `getSelectedVideo(event.id)?.id || 'empty'` to handle null/undefined cases
  </verify>
  <done>
{#key} block wraps VideoPlayer with video ID as key. Switching videos in timeline now recreates the player component cleanly.
</done>
</task>

</tasks>

<verification>
**Manual verification needed:**

1. Open the timeline view in browser (requires event with multiple videos)
2. Click a different video in the supporting video grid
3. Verify the featured player updates to show the new video (not the old video)
4. Verify the poster image matches the selected video
5. Check browser console for no errors

**No automated tests needed** - this is a UI reactivity fix best verified manually.
</verification>

<success_criteria>
- {#key} block wraps VideoPlayer with proper key expression
- Switching videos in supporting grid updates featured player correctly
- No stale video content or memory leaks when navigating timeline
- Video player clears when event has no selected video
</success_criteria>

<output>
After completion, create `.planning/phases/02-fix-video-playback/02-01-SUMMARY.md` with:
- Files modified count
- Key implementation decisions (why {#key} on video.id)
- Any deviations from plan
- Verification status
</output>

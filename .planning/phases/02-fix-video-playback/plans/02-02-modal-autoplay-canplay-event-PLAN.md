---
phase: 02-fix-video-playback
plan: 02
type: execute
wave: 2
depends_on: []
files_modified:
  - Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte
  - Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte
  - Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte
autonomous: true

must_haves:
  truths:
    - "Clicking video in event view modal plays the video automatically"
    - "Clicking video in media showcase grid modal plays the video automatically"
    - "Video stops playing and resets when modal closes"
  artifacts:
    - path: "Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte"
      provides: "Media showcase grid with modal playback"
      contains: "on:canplay"
    - path: "Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte"
      provides: "Mobile featured view with modal playback"
      contains: "on:canplay"
    - path: "Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte"
      provides: "Event detail view with modal playback"
      contains: "on:canplay"
  key_links:
    - from: "EventsGrid.svelte"
      to: "modal video element"
      via: "canplay event handler"
      pattern: "on:canplay.*handleCanPlay"
    - from: "EventsFeaturedOnly.svelte"
      to: "modal video element"
      via: "canplay event handler"
      pattern: "on:canplay.*handleCanPlay"
    - from: "EventVideosGrid.svelte"
      to: "modal video element"
      via: "canplay event handler"
      pattern: "on:canplay.*handleCanPlay"
---

<objective>
Fix modal video autoplay using `canplay` event instead of unreliable setTimeout, and add proper cleanup on modal close.

**Purpose:** Videos in modals should autoplay reliably when the modal opens. Current implementation uses setTimeout which may fire before the video is ready, causing playback to fail. This plan implements the `canplay` event pattern which fires only when the browser has enough data to play.

**Output:** Three modal components (EventsGrid, EventsFeaturedOnly, EventVideosGrid) with reliable canplay-based autoplay and proper cleanup.
</objective>

<execution_context>
@/Users/leinad/.claude/get-shit-done/workflows/execute-plan.md
@/Users/leinad/.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/phases/02-fix-video-playback/02-RESEARCH.md
@.planning/PROJECT.md
</context>

<tasks>

<task type="auto">
  <name>Add canplay autoplay handler to EventsGrid modal</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</files>
  <action>
In EventsGrid.svelte, replace the setTimeout-based autoplay with canplay event:

1. Add `let playAttempted = false;` variable in script section (after existing let declarations)
2. Modify `openVideoModal` function to reset playAttempted:
```javascript
const openVideoModal = (video) => {
  modalVideo = video;
  playAttempted = false; // ADD THIS LINE

  setTimeout(() => {
    pageModals.teleport_to($modalRoot);
  }, 300);
};
```
3. Add new `handleCanPlay` function:
```javascript
const handleCanPlay = () => {
  if (!playAttempted && modalVideoElement) {
    playAttempted = true;
    modalVideoElement.play().catch(error => {
      console.log('Autoplay prevented:', error.name);
      // User will need to click play manually - browser policy
    });
  }
};
```
4. Update `closeVideoModal` to properly cleanup:
```javascript
const closeVideoModal = () => {
  if (modalVideoElement) {
    modalVideoElement.pause();
    modalVideoElement.currentTime = 0; // ADD THIS
    modalVideoElement.src = ''; // ADD THIS - release memory
  }
  modalVideo = null;
};
```
5. Add `on:canplay={handleCanPlay}` to the video element (around line 141-151)

Keep existing setTimeout for Portal teleport - only remove/replace autoplay logic.
</action>
  <verify>
1. Run `grep "playAttempted" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 3 occurrences
2. Run `grep "on:canplay" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
3. Run `grep "currentTime = 0" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
4. Run `grep "src = ''" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
  </verify>
  <done>
EventsGrid modal uses canplay event for autoplay, resets video on close, no memory leaks from unclosed video elements.
</done>
</task>

<task type="auto">
  <name>Add canplay autoplay handler to EventsFeaturedOnly modal</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte</files>
  <action>
In EventsFeaturedOnly.svelte, replace the setTimeout-based autoplay with canplay event:

1. Add `let playAttempted = false;` variable in script section (after existing let declarations)
2. Modify `openVideoModal` function to reset playAttempted:
```javascript
const openVideoModal = (video) => {
  modalVideo = video;
  playAttempted = false; // ADD THIS LINE

  setTimeout(() => {
    pageModals.teleport_to($modalRoot);
  }, 300);
};
```
3. Add new `handleCanPlay` function:
```javascript
const handleCanPlay = () => {
  if (!playAttempted && modalVideoElement) {
    playAttempted = true;
    modalVideoElement.play().catch(error => {
      console.log('Autoplay prevented:', error.name);
      // User will need to click play manually - browser policy
    });
  }
};
```
4. Update `closeVideoModal` to properly cleanup:
```javascript
const closeVideoModal = () => {
  if (modalVideoElement) {
    modalVideoElement.pause();
    modalVideoElement.currentTime = 0; // ADD THIS
    modalVideoElement.src = ''; // ADD THIS - release memory
  }
  modalVideo = null;
};
```
5. Add `on:canplay={handleCanPlay}` to the video element (around line 88-98)

Keep existing setTimeout for Portal teleport - only remove/replace autoplay logic.
</action>
  <verify>
1. Run `grep "playAttempted" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 3 occurrences
2. Run `grep "on:canplay" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
3. Run `grep "currentTime = 0" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
4. Run `grep "src = ''" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
  </verify>
  <done>
EventsFeaturedOnly modal uses canplay event for autoplay, resets video on close, no memory leaks from unclosed video elements.
</done>
</task>

<task type="auto">
  <name>Add canplay autoplay handler to EventVideosGrid modal</name>
  <files>Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte</files>
  <action>
In EventVideosGrid.svelte, add canplay event for autoplay (no setTimeout exists currently):

1. Add `let playAttempted = false;` variable in script section (after existing let declarations)
2. Modify `openVideoModal` function to reset playAttempted:
```javascript
const openVideoModal = (video) => {
  modalVideo = video;
  playAttempted = false; // ADD THIS LINE
};
```
3. Add new `handleCanPlay` function:
```javascript
const handleCanPlay = () => {
  if (!playAttempted && modalVideoElement) {
    playAttempted = true;
    modalVideoElement.play().catch(error => {
      console.log('Autoplay prevented:', error.name);
      // User will need to click play manually - browser policy
    });
  }
};
```
4. Update `closeVideoModal` to properly cleanup:
```javascript
const closeVideoModal = () => {
  if (modalVideoElement) {
    modalVideoElement.pause();
    modalVideoElement.currentTime = 0; // ADD THIS
    modalVideoElement.src = ''; // ADD THIS - release memory
  }
  modalVideo = null;
};
```
5. Add `on:canplay={handleCanPlay}` to the video element (around line 102-112)

This component doesn't use Portal/teleport, so no setTimeout to keep.
</action>
  <verify>
1. Run `grep "playAttempted" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 3 occurrences
2. Run `grep "on:canplay" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
3. Run `grep "currentTime = 0" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
4. Run `grep "src = ''" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
  </verify>
  <done>
EventVideosGrid modal uses canplay event for autoplay, resets video on close, no memory leaks from unclosed video elements.
</done>
</task>

</tasks>

<verification>
**Manual verification needed for each modal:**

1. Open the media showcase grid (EventsGrid)
2. Click any video thumbnail
3. Verify modal opens and video starts playing automatically
4. Close modal and verify video stops
5. Reopen modal and verify video starts from beginning (not where it left off)

Repeat for EventsFeaturedOnly (mobile view) and EventVideosGrid (event detail view).

**Console check:**
- No "Autoplay prevented" messages should appear (muted autoplay in plan 02-03 will fix this)
- No errors related to video playback
</verification>

<success_criteria>
- All three modal components use canplay event for autoplay
- playAttempted flag prevents duplicate play() calls
- Modal close pauses video, resets to 0:00, and clears src to release memory
- Videos autoplay reliably across all modal views
</success_criteria>

<output>
After completion, create `.planning/phases/02-fix-video-playback/02-02-SUMMARY.md` with:
- Files modified count (3)
- Pattern applied (canplay + playAttempted flag)
- Cleanup pattern (pause + reset + clear src)
- Verification status
</output>

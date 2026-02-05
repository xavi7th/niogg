---
phase: 02-fix-video-playback
plan: 03
type: execute
wave: 3
depends_on: ["02"]
files_modified:
  - Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte
  - Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte
  - Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte
  - Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte
  - Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte
autonomous: true

must_haves:
  truths:
    - "Videos play inline on iOS devices (not fullscreen)"
    - "Autoplay works consistently across browsers (muted)"
    - "Video load errors show user-friendly message with retry button"
    - "Video titles display correctly in timeline and grid views"
  artifacts:
    - path: "Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte"
      contains: "playsinline"
      contains: "muted"
      contains: "on:error"
    - path: "Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte"
      contains: "playsinline"
      contains: "muted"
      contains: "on:error"
    - path: "Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte"
      contains: "playsinline"
      contains: "muted"
      contains: "on:error"
    - path: "Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte"
      contains: "playsinline"
      contains: "muted"
      contains: "on:error"
  key_links:
    - from: "All video elements"
      to: "iOS Safari"
      via: "playsinline attribute"
      pattern: "playsinline"
    - from: "All video elements"
      to: "browser autoplay policy"
      via: "muted autoplay"
      pattern: "muted.*autoplay"
    - from: "All video components"
      to: "error handling"
      via: "on:error handler"
      pattern: "on:error.*handleVideoError"
---

<objective>
Add mobile compatibility attributes (playsinline, muted) and error handling with retry buttons to all video components.

**Purpose:**
1. **playsinline**: Prevents iOS Safari from forcing fullscreen playback, keeping videos inside modals
2. **muted**: Ensures autoplay works consistently across all browsers by complying with autoplay policies
3. **Error handling**: Displays user-friendly messages when videos fail to load, with retry buttons for recovery

**Output:** All video components with playsinline/muted attributes and error handling UI.
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
  <name>Add playsinline/muted and error handling to EventsGrid modal</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte</files>
  <action>
In EventsGrid.svelte, add mobile attributes and error handling:

1. Add error state variables in script section:
```javascript
let videoError = null;
let retryCount = 0;
const MAX_RETRIES = 3;
```

2. Add error handler function:
```javascript
const handleVideoError = (event) => {
  const video = event.target;
  const error = video.error;

  if (retryCount < MAX_RETRIES) {
    console.error('Video load error:', error);
    videoError = {
      code: error?.code || 'UNKNOWN',
      message: getVideoErrorMessage(error?.code),
      retryable: retryCount < MAX_RETRIES
    };
  }
};

const getVideoErrorMessage = (code) => {
  switch (code) {
    case 1: return 'Video fetching aborted';
    case 2: return 'Network error during video load';
    case 3: return 'Video decoding error';
    case 4: return 'Video format or source not supported';
    default: return 'Unable to load video';
  }
};

const handleRetry = () => {
  if (retryCount < MAX_RETRIES) {
    retryCount++;
    videoError = null;
    if (modalVideoElement) {
      modalVideoElement.load();
      // handleCanPlay will trigger play() on next canplay event
    }
  }
};
```

3. Update `openVideoModal` to reset error state:
```javascript
const openVideoModal = (video) => {
  modalVideo = video;
  playAttempted = false;
  videoError = null; // ADD THIS
  retryCount = 0; // ADD THIS
  // ... rest of function
};
```

4. Add `playsinline`, `muted`, and `on:error` to video element:
```svelte
<video
  bind:this={modalVideoElement}
  src={modalVideo.video_url}
  poster={modalVideo.thumbnail_url}
  controls
  autoplay
  muted
  playsinline
  preload="metadata"
  on:canplay={handleCanPlay}
  on:error={handleVideoError}
  class="modal-video-element"
>
```

5. Add error UI inside modal-video-wrapper (before video element):
```svelte
{#if videoError}
  <div class="video-error-overlay">
    <p class="error-message">{videoError.message}</p>
    {#if videoError.retryable && retryCount < MAX_RETRIES}
      <button on:click={handleRetry} class="btn-retry">Retry</button>
    {/if}
  </div>
{/if}
```

6. Add error styling to style section:
```css
.video-error-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #000;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  z-index: 5;
  padding: 2rem;
  text-align: center;
}

.error-message {
  color: white;
  font-size: 1rem;
  margin: 0;
}

.btn-retry {
  background-color: #ff7607;
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.25rem;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.btn-retry:hover {
  background-color: #e66d06;
}
```
</action>
  <verify>
1. Run `grep "playsinline" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
2. Run `grep "muted" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
3. Run `grep "on:error" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find 1 match
4. Run `grep "videoError" Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - should find at least 3 occurrences
  </verify>
  <done>
EventsGrid modal has playsinline/muted attributes, error UI shows when video fails to load, retry button allows recovery.
</done>
</task>

<task type="auto">
  <name>Add playsinline/muted and error handling to EventsFeaturedOnly modal</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte</files>
  <action>
In EventsFeaturedOnly.svelte, add mobile attributes and error handling (same pattern as EventsGrid):

1. Add error state variables in script section:
```javascript
let videoError = null;
let retryCount = 0;
const MAX_RETRIES = 3;
```

2. Add error handler functions (handleVideoError, getVideoErrorMessage, handleRetry) - same as EventsGrid

3. Update `openVideoModal` to reset error state (videoError = null, retryCount = 0)

4. Add `playsinline`, `muted`, and `on:error` to video element

5. Add error UI inside modal-video-wrapper (before video element)

6. Add error styling to style section

Follow exact same pattern as EventsGrid task.
</action>
  <verify>
1. Run `grep "playsinline" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
2. Run `grep "muted" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
3. Run `grep "on:error" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find 1 match
4. Run `grep "videoError" Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - should find at least 3 occurrences
  </verify>
  <done>
EventsFeaturedOnly modal has playsinline/muted attributes, error UI shows when video fails to load, retry button allows recovery.
</done>
</task>

<task type="auto">
  <name>Add playsinline/muted and error handling to EventVideosGrid modal</name>
  <files>Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte</files>
  <action>
In EventVideosGrid.svelte, add mobile attributes and error handling (same pattern as EventsGrid):

1. Add error state variables in script section:
```javascript
let videoError = null;
let retryCount = 0;
const MAX_RETRIES = 3;
```

2. Add error handler functions (handleVideoError, getVideoErrorMessage, handleRetry) - same as EventsGrid

3. Update `openVideoModal` to reset error state (videoError = null, retryCount = 0)

4. Add `playsinline`, `muted`, and `on:error` to video element

5. Add error UI inside modal-video-wrapper (before video element)

6. Add error styling to style section

Follow exact same pattern as EventsGrid task.
</action>
  <verify>
1. Run `grep "playsinline" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
2. Run `grep "muted" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
3. Run `grep "on:error" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find 1 match
4. Run `grep "videoError" Modules/PublicPage/resources/js/Pages/EventVideosGrid.svelte` - should find at least 3 occurrences
  </verify>
  <done>
EventVideosGrid modal has playsinline/muted attributes, error UI shows when video fails to load, retry button allows recovery.
</done>
</task>

<task type="auto">
  <name>Add playsinline/muted to VideoPlayer component</name>
  <files>Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte</files>
  <action>
In VideoPlayer.svelte, add mobile compatibility attributes:

1. Add `playsinline` and `muted` attributes to the video element (around line 53-63):
```svelte
<video
  bind:this={videoElement}
  poster={video.thumbnail_url}
  controls
  preload="metadata"
  playsinline
  muted
  on:play={handlePlay}
  on:pause={handlePause}
>
```

Note: VideoPlayer doesn't have autoplay (user clicks play button), so we only add playsinline/muted for compatibility.

No error handling needed here - VideoPlayer is used in timeline where video selection is controlled by parent component.
</action>
  <verify>
1. Run `grep "playsinline" Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte` - should find 1 match
2. Run `grep "muted" Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte` - should find 1 match
  </verify>
  <done>
VideoPlayer has playsinline/muted attributes for mobile compatibility.
</done>
</task>

<task type="manual">
  <name>Verify and fix video title display (SC-4)</name>
  <files>
    - Modules/PublicPage/resources/js/Pages/Components/SupportingVideoGrid.svelte
    - Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte
    - Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte
    - Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte
  </files>
  <action>
Verify video titles display correctly in all views and fix any CSS visibility issues.

**Check locations where titles should display:**

1. **SupportingVideoGrid (timeline supporting videos):** `.card-title` at line 58
2. **VideoPlayer (timeline featured video):** `.video-title` at line 111
3. **EventsGrid modal:** `.modal-title` at line 154
4. **EventsFeaturedOnly modal:** `.modal-title` at line 101
5. **EventsGrid card:** `.card-title` at line 109

**Verification steps:**

1. Check all title CSS rules for any hiding issues:
   - Look for: `text-indent: -9999px`, `display: none`, `visibility: hidden`, `opacity: 0`, `font-size: 0`
   - Run: `grep -rn "text-indent\|display.*none\|visibility.*hidden" Modules/PublicPage/resources/js/Pages/Components/ | grep -i title`

2. Verify title data is present in component props:
   - Confirm `video.title` is passed correctly to each component
   - Check for any undefined/null handling that might hide titles

3. If CSS hiding is found, fix it:
   - Remove any negative `text-indent` values from title classes
   - Ensure titles have visible colors (not transparent, good contrast)
   - Check for parent containers that might be hiding overflow improperly

4. If title elements are missing entirely:
   - Add title display to the appropriate HTML structure
   - Follow existing patterns from other components

**Expected result:** Video titles are visible in all grid views, timeline views, and modal overlays.
</action>
  <verify>
1. Check titles in timeline view - open EventTimeline page
2. Check titles in grid modal - open any video in EventsGrid
3. Check titles in SupportingVideoGrid - scroll to "More from this event" section
4. Run `grep "video\.title\|modalVideo\.title" Modules/PublicPage/resources/js/Pages/Components/*.svelte` to verify all title references exist
5. Run `grep -n "text-indent" Modules/PublicPage/resources/js/Pages/Components/*.svelte | grep -v "title"` should not find title classes with negative text-indent
  </verify>
  <done>
Video titles display correctly in timeline (SupportingVideoGrid and VideoPlayer) and grid modals (EventsGrid, EventsFeaturedOnly). No CSS hiding rules found affecting title visibility.
</done>
</task>

</tasks>

<verification>
**Manual verification needed:**

1. **iOS/Safari testing (if available):**
   - Open any modal on iOS device or Safari
   - Verify video plays inline (not fullscreen)
   - Verify autoplay works (muted)

2. **Error handling testing:**
   - Open browser DevTools Network tab
   - Block video URL or use invalid URL
   - Open modal
   - Verify error message displays
   - Click retry button
   - Verify retry counter increments

3. **Autoplay verification:**
   - Open any modal
   - Verify video starts playing automatically (muted)
   - User can unmute via native controls

4. **Video titles (SC-4, THUMB-07):**
   - Check timeline view - titles visible in SupportingVideoGrid cards and VideoPlayer
   - Check modal views - titles display correctly in modal-info section
   - Check grid views - titles visible in EventsGrid card titles
   - Verify no CSS hiding rules (text-indent, display: none) affect title visibility

**Console check:**
- No unhandled video errors
- Error messages logged when video fails to load
</verification>

<success_criteria>
- All video elements have playsinline attribute (iOS compatibility)
- All video elements have muted attribute (autoplay policy compliance)
- Error UI displays when video fails to load
- Retry button allows up to 3 retry attempts
- Videos autoplay muted on modal open
- Users can unmute via native controls
- Video titles display correctly in all views
</success_criteria>

<output>
After completion, create `.planning/phases/02-fix-video-playback/02-03-SUMMARY.md` with:
- Files modified count (5)
- Mobile attributes added (playsinline, muted)
- Error handling pattern implemented
- Verification status including iOS testing if available
</output>

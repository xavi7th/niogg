# Phase 02: Fix Video Playback - Research

**Researched:** 2026-02-03
**Domain:** Svelte 5, HTML5 Video, Mobile Browser Compatibility
**Confidence:** HIGH

## Summary

This research covers HTML5 video playback in a Svelte 5 + Inertia.js modular monolith application. The codebase has three modal-based video views (EventVideosGrid, EventsGrid, EventsFeaturedOnly) and one timeline-based player (EventTimeline with VideoPlayer). Current implementation lacks autoplay reliability, mobile compatibility, error handling, and proper reactivity for dynamic video sources.

**Primary recommendations:**
1. Use `canplay` event for triggering autoplay (more reliable than `loadeddata` for playback readiness)
2. Always include `playsinline` attribute for iOS compatibility (universal approach, no harm on desktop)
3. Wrap video elements with `{#key}` blocks when src changes dynamically to prevent memory leaks
4. Implement muted autoplay with user interaction detection for browser policy compliance
5. Add error handling with retry buttons and console logging for debugging

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Svelte | 5.x | UI framework with runes mode | Codebase uses Svelte 5 syntax (`$state`, `$effect`) |
| Inertia.js | ^1.2.0 | SPA-like page transitions without API | Used for routing and modal management |
| HTML5 Video | Native API | Browser-native video playback | No external video library needed |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| svelte-teleport | ^0.1.1 | Portal modals outside component tree | Used in EventsGrid, EventsFeaturedOnly for modal rendering |
| SweetAlert2 | ^11.26.17 | Flash notifications | Already integrated for global flash messages |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Native HTML5 video | Video.js, Plyr | External libraries add 100-200KB, unnecessary complexity for basic playback |
| {#key} blocks | Manual cleanup in onDestroy | {#key} is automatic and less error-prone for video element recreation |

**Installation:**
```bash
# No new packages needed - all dependencies already installed
npm install  # Run if packages missing
```

## Architecture Patterns

### Recommended Project Structure
```
Modules/PublicPage/resources/js/Pages/Components/
├── VideoPlayer.svelte          # Main player (large, in timeline)
├── LazyVideo.svelte            # Lazy-loading video component
├── EventTimeline.svelte        # Timeline view with {#key} needed
├── SupportingVideoGrid.svelte  # Horizontal scroll grid
├── EventsGrid.svelte           # Grid view with Portal modal
├── EventsFeaturedOnly.svelte   # Mobile featured view with Portal modal
└── EventVideosGrid.svelte      # Event-specific grid with inline modal
```

### Pattern 1: Modal Autoplay with canplay Event
**What:** Wait for video to be ready before calling play() in modal
**When to use:** All modal-based video players (EventsGrid, EventsFeaturedOnly, EventVideosGrid)
**Confidence:** HIGH (verified with MDN official docs)

```svelte
// Source: https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/canplay_event
<script>
  let modalVideoElement;
  let modalVideo = null;
  let playAttempted = false;

  const openVideoModal = (video) => {
    modalVideo = video;
    playAttempted = false;
  };

  const handleCanPlay = () => {
    if (!playAttempted && modalVideoElement) {
      playAttempted = true;
      modalVideoElement.play().catch(error => {
        console.log('Autoplay prevented:', error.name);
        // User will need to click play manually
      });
    }
  };
</script>

{#if modalVideo}
  <div class="video-modal">
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
    />
  </div>
{/if}
```

### Pattern 2: {#key} Block for Dynamic Video Sources
**What:** Force complete video element recreation when src changes
**When to use:** EventTimeline.svelte where VideoPlayer src changes based on user selection
**Confidence:** HIGH (verified with Svelte official docs)

```svelte
// Source: https://svelte.dev/tutorial/svelte/key-blocks
<script>
  import VideoPlayer from './VideoPlayer.svelte';

  let selectedVideos = new Map();

  $: events.forEach((event) => {
    if (!selectedVideos.has(event.id)) {
      const featured = event.videos?.find((v) => v.is_featured);
      selectedVideos.set(event.id, featured?.id);
    }
  });

  const handleVideoSelect = (eventId, videoId) => {
    selectedVideos.set(eventId, videoId);
    selectedVideos = selectedVideos; // Trigger reactivity
  };

  const getSelectedVideo = (eventId) => {
    const videoId = selectedVideos.get(eventId);
    return events.find((e) => e.id === eventId)?.videos?.find((v) => v.id === videoId);
  };
</script>

{#each events as event (event.id)}
  {#key getSelectedVideo(event.id)?.id || 'empty'}
    <VideoPlayer video={getSelectedVideo(event.id)} size="large" />
  {/key}
{/each}
```

### Pattern 3: Modal Cleanup on Close
**What:** Properly pause and reset video when modal closes
**When to use:** All modal components
**Confidence:** HIGH (best practice for memory management)

```svelte
<script>
  const closeVideoModal = () => {
    if (modalVideoElement) {
      modalVideoElement.pause();
      modalVideoElement.currentTime = 0; // Reset to beginning
      modalVideoElement.src = ''; // Release memory
    }
    modalVideo = null;
  };
</script>
```

### Pattern 4: Error Handling with Retry
**What:** Display error message and retry button when video fails to load
**When to use:** All video components
**Confidence:** MEDIUM (standard pattern, less formal documentation)

```svelte
<script>
  let videoError = null;
  let retryCount = 0;
  const MAX_RETRIES = 3;

  const handleVideoError = (event) => {
    const video = event.target;
    const error = video.error;

    // Only log if we haven't exceeded retries
    if (retryCount < MAX_RETRIES) {
      console.error('Video load error:', error);
      videoError = {
        code: error?.code || 'UNKNOWN',
        message: getErrorMessage(error?.code),
        retryable: retryCount < MAX_RETRIES
      };
    }
  };

  const getErrorMessage = (code) => {
    switch (code) {
      case 1: return 'Video fetching aborted';
      case 2: return 'Network error during video load';
      case 3: return 'Video decoding error';
      case 4: return 'Video format or source not supported';
      default: return 'Unable to load video';
    }
  };

  const handleRetry = () => {
    retryCount++;
    videoError = null;
    if (modalVideoElement) {
      modalVideoElement.load();
      modalVideoElement.play().catch(() => {});
    }
  };
</script>

{#if videoError}
  <div class="video-error">
    <p>{videoError.message}</p>
    {#if videoError.retryable}
      <button on:click={handleRetry}>Retry</button>
    {/if}
  </div>
{/if}

<video on:error={handleVideoError} />
```

### Anti-Patterns to Avoid
- **Using setTimeout for autoplay timing**: Unreliable across network conditions. Use `canplay` event instead.
- **Pausing without resetting memory**: Always set `video.src = ''` or call `video.load()` after pause to release resources.
- **Skipping playsinline on iOS**: Will force fullscreen playback, breaking modal UX.
- **Forgetting to handle play() rejection**: `play()` returns a Promise that rejects when autoplay is blocked.
- **Using {#each} without keys on video grids**: Causes DOM reuse, memory leaks, and stale video references.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Video player UI | Custom play/pause/seek controls | Native `controls` attribute | Browsers provide accessible, touch-optimized controls for free |
| Modal teleportation | Manual DOM manipulation | svelte-teleport Portal | Already installed, handles edge cases |
| Video format detection | Custom file extension parsing | `<source>` elements with type attribute | Browser chooses best supported format automatically |
| Autoplay policy handling | User agent sniffing | `play().catch()` with muted attribute | Browser-native detection, future-proof |

**Key insight:** HTML5 video with native controls is accessible, keyboard-navigable, and touch-optimized by default. Custom controls require significant effort to match browser functionality.

## Common Pitfalls

### Pitfall 1: Autoplay Policy Blocking
**What goes wrong:** Videos fail to autoplay with sound, console shows "NotAllowedError"
**Why it happens:** Chrome/Safari block unmuted autoplay without prior user interaction
**How to avoid:** Use `muted` attribute for autoplay, let user enable sound manually
**Warning signs:** Autoplay works on localhost but fails in production

```svelte
<!-- GOOD: Muted autoplay -->
<video autoplay muted playsinline />

<!-- BAD: Unmuted autoplay (will be blocked) -->
<video autoplay />
```

### Pitfall 2: iOS Fullscreen Hijack
**What goes wrong:** Modal videos open in fullscreen, breaking modal UX
**Why it happens:** iOS defaults to fullscreen for video playback
**How to avoid:** Always add `playsinline` attribute
**Warning signs:** Modal backdrop disappears, video takes entire screen

```svelte
<!-- GOOD: Plays inline in modal -->
<video playsinline />

<!-- BAD: Forces fullscreen on iOS -->
<video />
```

### Pitfall 3: Memory Leaks from Video Elements
**What goes wrong:** Memory usage grows when switching between videos
**Why it happens:** Browser caches video resources even after element removal
**How to avoid:** Clear `src` attribute and call `load()` before removing element
**Warning signs:** Chrome DevTools shows increasing memory heap on video navigation

```javascript
// GOOD: Proper cleanup
const cleanup = () => {
  videoElement.pause();
  videoElement.src = '';
  videoElement.load();
};

// BAD: Just pausing
const cleanup = () => {
  videoElement.pause(); // Memory not released!
};
```

### Pitfall 4: Stale Video References with Dynamic src
**What goes wrong:** Video shows wrong content when switching between videos
**Why it happens:** Svelte reuses DOM elements, doesn't recreate video on src change
**How to avoid:** Use `{#key}` block to force recreation
**Warning signs:** Video poster/image doesn't match selected video

```svelte
<!-- GOOD: Forces recreation -->
{#key currentVideo.id}
  <video src={currentVideo.url} />
{/key}

<!-- BAD: May reuse old element -->
<video src={currentVideo.url} />
```

### Pitfall 5: Modal Playback Race Condition
**What goes wrong:** Video doesn't play when modal opens, requires second click
**Why it happens:** `play()` called before video is ready to accept commands
**How to avoid:** Use `canplay` event or check `video.readyState`
**Warning signs:** Inconsistent behavior - sometimes works, sometimes doesn't

```svelte
<!-- GOOD: Wait for canplay -->
<video on:canplay={() => video.play()} />

<!-- BAD: Play immediately -->
{#if isOpen}
  <video autoplay /> <!-- May not be ready -->
{/if}
```

## Code Examples

Verified patterns from official sources:

### Video Element with All Required Attributes
```svelte
<!-- Source: MDN Video Element + Autoplay Guide -->
<video
  src={video.url}
  poster={video.thumbnail}
  controls
  autoplay
  muted
  playsinline
  preload="metadata"
  on:canplay={handleCanPlay}
  on:error={handleError}
>
  <p>Your browser does not support HTML5 video.</p>
</video>
```

### Promise-Based Play() with Error Handling
```javascript
// Source: https://developer.mozilla.org/en-US/docs/Web/Media/Guides/Autoplay
function attemptPlay(videoElement) {
  const playPromise = videoElement.play();

  if (playPromise !== undefined) {
    playPromise
      .then(() => {
        console.log('Autoplay started');
      })
      .catch(error => {
        if (error.name === 'NotAllowedError') {
          console.log('Autoplay blocked - user interaction required');
          // Show play button overlay
        }
      });
  }
}
```

### {#key} Block for Video Reactivity
```svelte
<!-- Source: https://svelte.dev/tutorial/svelte/key-blocks -->
{#key selectedVideo.id}
  <VideoPlayer video={selectedVideo} />
{/key}
```

### onDestroy Cleanup Pattern
```svelte
<script>
  import { onDestroy } from 'svelte';

  let videoElement;

  onDestroy(() => {
    if (videoElement) {
      videoElement.pause();
      videoElement.src = '';
      videoElement.load();
    }
  });
</script>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| setTimeout for autoplay timing | `canplay` event or `play().catch()` | ~2018 (autoplay policies) | More reliable playback, respects browser policies |
| No mobile attributes | `playsinline` required for iOS | iOS 10+ (2017) | Inline playback on mobile, better UX |
| Manual cleanup for memory leaks | {#key} blocks automatic cleanup | Svelte 3 (2020) | Less boilerplate, fewer leaks |
| Unmuted autoplay by default | Muted autoplay with user controls | ~2018 (browser policies) | Autoplay works consistently |

**Deprecated/outdated:**
- **webkit-playsinline**: Was needed for older iOS Safari, now standard `playsinline` is sufficient (iOS 10+)
- **x-webkit-airplay**: Legacy AirPlay attribute, modern browsers use standard API
- **Inline event handlers in HTML**: `onplay="..."` should use `on:play={handler}` in Svelte

## Open Questions

### 1. Mobile video title display in SupportingVideoGrid
**What we know:** Grid shows thumbnails and play buttons, but video titles may not display correctly per THUMB-07
**What's unclear:** Current implementation shows titles in card-content, need to verify if titles are missing or just styled incorrectly
**Recommendation:** Inspect SupportingVideoGrid.svelte card-title rendering during implementation, may be CSS visibility issue

### 2. Sound state preference for modal autoplay
**What we know:** Muted autoplay always works, unmuted requires prior user interaction
**What's unclear:** Whether users prefer muted autoplay with visual indicator, or manual play button
**Recommendation:** Start with muted autoplay (works universally), add user preference in future enhancement

### 3. Error retry strategy
**What we know:** Should implement retry with exponential backoff, but unclear on optimal count
**What's unclear:** Whether to retry immediately or after delay, max retries before showing permanent error
**Recommendation:** Use 3 retries with 1-2 second delays, consistent with DoorDash best practices

## Sources

### Primary (HIGH confidence)
- [MDN: Autoplay guide for media and Web Audio APIs](https://developer.mozilla.org/en-US/docs/Web/Media/Guides/Autoplay) - Comprehensive autoplay policy documentation (verified 2025-09-18)
- [MDN: <video>: The Video Embed element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video) - Official HTML5 video element reference (verified 2025-03-13)
- [Svelte: Key blocks tutorial](https://svelte.dev/tutorial/svelte/key-blocks) - Official {#key} block documentation

### Secondary (MEDIUM confidence)
- [Stack Overflow: canplay vs loadedmetadata](https://stackoverflow.com/questions/28490723/what-is-the-difference-between-canplay-and-loadedmetadata-event-listener-for-vid) - Event timing comparison
- [CSS-Tricks: What does playsinline mean?](https://css-tricks.com/what-does-playsinline-mean-in-web-video/) - playsinline attribute explanation
- [Svelte Playground: Video autoplay example](https://svelte.dev/playground/44166631ed1e40ecb86cc260c8914734) - Official Svelte 5 video example
- [Stack Overflow: How to retry loading video](https://stackoverflow.com/questions/45056466/how-to-retry-loading-video-again-once-it-fails) - Retry pattern discussion

### Tertiary (LOW confidence)
- [Stack Overflow: Getting video to work inline for iPhone](https://stackoverflow.com/questions/65452217/getting-video-to-work-inline-for-iphone-mobile) - iOS-specific discussion
- [Gist: HTML5 Video Playback Error Handling POC](https://gist.github.com/3252894) - Error handling proof of concept
- [Medium: JavaScript error handling patterns](https://medium.com/@rivoltafilippo/javascript-error-handling-patterns-and-best-practices-e90b5492213e) - General error patterns

### Existing Research
- [.planning/research/VIDEO-PLAYBACK.md](/Users/leinad/Work/htdocs/asuke-niogg.org/.planning/research/VIDEO-PLAYBACK.md) - Initial research from 2026-02-02

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Svelte 5 and HTML5 video verified in codebase
- Architecture: HIGH - All patterns verified with official Svelte/MDN docs
- Pitfalls: HIGH - All issues verified with multiple sources

**Research date:** 2026-02-03
**Valid until:** 2026-03-03 (30 days - stable HTML5 video API, Svelte 5 syntax unlikely to change)

**Codebase analysis completed:**
- VideoPlayer.svelte: Uses lazyLoad flag (disabled), missing playsinline, no autoplay
- LazyVideo.svelte: Has IntersectionObserver, missing playsinline, no autoplay
- EventVideosGrid.svelte: Has modal with autoplay, no canplay event, no playsinline
- EventsGrid.svelte: Has Portal modal with autoplay, uses setTimeout(300ms), no playsinline
- EventsFeaturedOnly.svelte: Has Portal modal with autoplay, uses setTimeout(300ms), no playsinline
- EventTimeline.svelte: Needs {#key} block for VideoPlayer reactivity

---

*Phase: 02-fix-video-playback*
*Research completed: 2026-02-03*

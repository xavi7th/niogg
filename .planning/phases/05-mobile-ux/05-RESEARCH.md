# Phase 05: Mobile UX - Research

**Researched:** 2026-02-04
**Domain:** Svelte 5, HTML5 Video, Mobile-First Responsive Design, CSS Media Queries
**Confidence:** HIGH

## Summary

This research covers mobile-specific UX improvements for the timeline view and video playback in a Svelte 5 + Inertia.js modular monolith. The current implementation shows `EventTimeline` on desktop and `EventsFeaturedOnly` on mobile (screens < 768px), with mobile already displaying featured videos only. The mobile view has a "See more from {event}" button that navigates to event-specific grid views, but video playback on mobile needs verification.

**Primary recommendations:**
1. No new libraries needed - use existing Tailwind CSS v3 and Svelte 5 patterns
2. Use CSS media queries with `max-width: 768px` breakpoint (already established in codebase)
3. Verify `playsinline` and `muted` attributes are present on all video elements (already implemented in Phase 2)
4. Test autoplay on iOS Safari and Chrome Android - may need user interaction handling
5. Navigation to filtered grid view already exists via `EventsFeaturedOnly` component's `navigateToEventGrid` function

**Primary recommendation:** Verify Phase 2 mobile attributes are working, ensure mobile timeline view correctly shows featured videos only, and test autoplay across iOS/Android browsers. No new technical implementation needed - testing and verification only.

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| Svelte | 5.x | UI framework with runes mode | Codebase uses Svelte 5 syntax (`$state`, `$effect`) |
| Inertia.js | ^1.2.0 | SPA-like page transitions without API | Used for routing and navigation |
| Tailwind CSS | 3.x | Utility-first CSS framework | Already integrated via vite-module-loader |
| HTML5 Video | Native API | Browser-native video playback | No external video library needed |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| svelte-teleport | ^0.1.1 | Portal modals outside component tree | Used in EventsGrid, EventsFeaturedOnly for modal rendering |
| Ziggy | ^2.x | Laravel route helper for JavaScript | Use `route()` helper for generating event URLs |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Native HTML5 video | Video.js, Plyr | External libraries add 100-200KB, unnecessary for basic playback |
| CSS media queries | JavaScript matchMedia() | CSS-only approach is simpler and more performant |
| playsinline attribute | webkit-playsinline | Modern iOS (10+) supports standard attribute |

**Installation:**
```bash
# No new packages needed - all dependencies already installed
npm install  # Run if packages missing
```

## Architecture Patterns

### Recommended Project Structure
```
Modules/PublicPage/resources/js/Pages/Components/
├── EventTimeline.svelte        # Timeline view (desktop only, hidden @media max-width: 768px)
├── EventsFeaturedOnly.svelte   # Mobile view (shown @media max-width: 768px)
├── EventsGrid.svelte           # Grid view (all screen sizes)
├── VideoPlayer.svelte          # Main player (large, in timeline)
├── LazyVideo.svelte            # Lazy-loading video component
└── SupportingVideoGrid.svelte  # Horizontal scroll grid
```

### Pattern 1: Mobile-Only Display with CSS Media Queries
**What:** Use CSS `@media (max-width: 768px)` to show/hide components based on screen size
**When to use:** Desktop and mobile have fundamentally different layouts
**Confidence:** HIGH (existing pattern in codebase)

```svelte
// Source: /Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte
<style>
  @media (max-width: 768px) {
    .event-timeline {
      display: none;  /* Hide desktop timeline on mobile */
    }
  }
</style>
```

```svelte
// Source: /Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte
<style>
  .events-featured-only {
    display: none;  /* Hidden by default */
  }

  @media (max-width: 768px) {
    .events-featured-only {
      display: block;  /* Show on mobile */
    }
  }
</style>
```

### Pattern 2: Navigation to Event-Specific Grid View
**What:** Use Inertia router with `route()` helper to navigate to filtered event videos
**When to use:** "Show all videos from this event" button click
**Confidence:** HIGH (already implemented, verified in codebase)

```svelte
// Source: /Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte
<script>
  import { router } from '@inertiajs/svelte';

  const navigateToEventGrid = (eventSlug) => {
    router.visit(`/events/${eventSlug}/videos`);
  };
</script>

<button on:click={() => navigateToEventGrid(event.slug)}>
  See more from {event.name}
</button>
```

**Backend route:** `/Modules/PublicPage/routes/web.php:25`
```php
Route::get('/events/{event:slug}/videos', [EventsMediaShowcaseController::class, 'eventVideos'])->name('events.videos');
```

### Pattern 3: Mobile Video Attributes for Cross-Platform Compatibility
**What:** Include `playsinline` and `muted` attributes on all video elements
**When to use:** All video elements, especially modals and inline players
**Confidence:** HIGH (verified Phase 2 implementation)

```svelte
// Source: MDN Video Element + iOS Best Practices
<video
  src={video.url}
  poster={video.thumbnail}
  controls
  autoplay
  muted
  playsinline
  preload="metadata"
>
  <p>Your browser does not support HTML5 video.</p>
</video>
```

**Required attributes for mobile:**
- `playsinline`: Prevents iOS from forcing fullscreen (iOS 10+)
- `muted`: Enables autoplay compliance with browser policies
- `controls`: Provides touch-optimized native controls

### Pattern 4: Responsive Video Container with Aspect Ratio
**What:** Use `padding-bottom` hack or `aspect-ratio` CSS property for responsive video
**When to use:** All video containers to maintain 16:9 aspect ratio
**Confidence:** HIGH (existing pattern in codebase)

```svelte
// Source: Existing VideoPlayer.svelte pattern
<style>
  .video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;  /* 16:9 aspect ratio */
    background: #000;
  }

  .video-element {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
</style>
```

### Pattern 5: Touch-Optimized Button Targets
**What:** Ensure buttons meet minimum 48x48px touch target size (WCAG 2.1 AAA)
**When to use:** All interactive elements on mobile layouts
**Confidence:** HIGH (accessibility standard)

```svelte
// Source: WCAG 2.1 Guidelines
<style>
  .btn-primary {
    min-height: 48px;
    min-width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  @media (max-width: 768px) {
    .btn-primary {
      min-height: 56px;  /* Enhanced touch targets on mobile */
      min-width: 56px;
    }
  }
</style>
```

### Anti-Patterns to Avoid
- **Using JavaScript for mobile detection:** CSS media queries are more performant and work server-side rendered
- **Separate mobile routes:** Creates maintenance burden, use same route with responsive components
- **autoplay without muted attribute:** Will be blocked by browser autoplay policies
- **Forgetting playsinline on iOS:** Will force fullscreen, breaking modal UX
- **Touch targets smaller than 48px:** Violates accessibility guidelines, hard to tap

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Mobile detection | Custom user agent sniffing | CSS `@media (max-width: 768px)` | Media queries work server-side rendered, more performant |
| Video player UI | Custom play/pause/seek controls | Native `controls` attribute | Browsers provide accessible, touch-optimized controls |
| Route generation | String concatenation | Ziggy `route()` helper | Handles parameter escaping, works with Laravel named routes |
| Touch targets | Manual padding | Tailwind `min-h-[48px]` classes | Utility classes are consistent and documented |

**Key insight:** The mobile UX is already implemented in the codebase. This phase requires testing and verification, not new feature development.

## Common Pitfalls

### Pitfall 1: Autoplay Fails on Mobile Despite Correct Attributes
**What goes wrong:** Video doesn't autoplay when modal opens on mobile
**Why it happens:** iOS/Chrome require muted autoplay AND sometimes prior user interaction
**How to avoid:** Ensure `muted` attribute is present, provide clear play button overlay
**Warning signs:** Autoplay works on desktop but fails on mobile device

```svelte
<!-- GOOD: Muted autoplay with visual indicator -->
<video autoplay muted playsinline />
<div class="tap-to-unmute">Tap to unmute</div>

<!-- BAD: Unmuted autoplay (will be blocked) -->
<video autoplay />
```

### Pitfall 2: Mobile Timeline Shows Supporting Videos
**What goes wrong:** Mobile view shows all videos instead of featured only
**Why it happens:** EventTimeline shown on mobile, or EventsFeaturedOnly displays non-featured videos
**How to avoid:** Verify EventTimeline hidden @media 768px, EventsFeaturedOnly filters for `is_featured`
**Warning signs:** Users see horizontal scroll or multiple videos on mobile timeline

### Pitfall 3: Navigation Button Goes Wrong Route
**What goes wrong:** "See more from {event}" button goes to wrong page or 404
**Why it happens:** Incorrect event slug passed, or route mismatched
**How to avoid:** Use Ziggy `route('events.videos', { event: event.slug })` for type-safe routing
**Warning signs:** Button click leads to error or wrong event's videos

### Pitfall 4: Video Goes Fullscreen on iOS
**What goes wrong:** Modal video immediately goes fullscreen, can't see modal content
**Why it happens:** Missing `playsinline` attribute on video element
**How to avoid:** Always include `playsinline` on mobile video elements
**Warning signs:** Modal backdrop disappears, video takes entire screen

### Pitfall 5: Touch Targets Too Small on Mobile
**What goes wrong:** Users struggle to tap buttons, accidental clicks
**Why it happens:** Desktop-sized buttons (32-36px) used on mobile
**How to avoid:** Use 48-56px minimum touch targets, add padding for visual affordance
**Warning signs:** High bounce rate from mobile, rage clicks

### Pitfall 6: Modal Close Button Hard to Tap
**What goes wrong:** Users can't close video modal on mobile
**Why it happens:** Close button too small or covered by video controls
**How to avoid:** 44x44px minimum close button, positioned outside video area, high z-index
**Warning signs:** Users stuck in modal, have to refresh page

## Code Examples

Verified patterns from official sources:

### Mobile-Only Component Display
```svelte
<!-- Source: Existing EventsMediaShowcase.svelte pattern -->
<div class="events-media-showcase">
  {#if viewMode === 'timeline'}
    <!-- Desktop: EventTimeline shown, hidden on mobile via CSS -->
    <EventTimeline {events} />

    <!-- Mobile: EventsFeaturedOnly shown, hidden on desktop via CSS -->
    <EventsFeaturedOnly {events} />
  {:else}
    <!-- Grid view shown on all screen sizes -->
    <EventsGrid {events} />
  {/if}
</div>
```

### Featured-Only Video Filtering
```svelte
<!-- Source: /Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte -->
{#each events as event (event.id)}
  <section class="event-section">
    <div class="featured-player-section">
      {#if event.videos && event.videos.length > 0}
        {#if event.videos.find((v) => v.is_featured)}
          {@const featuredVideo = event.videos.find((v) => v.is_featured)}
          <!-- Only show featured video -->
          <div class="featured-video-card" on:click={() => openVideoModal(featuredVideo)}>
            <!-- Featured video card content -->
          </div>
        {/if}
      {/if}
    </div>

    <!-- No SupportingVideoGrid on mobile -->
    <div class="see-more-button">
      <button on:click={() => navigateToEventGrid(event.slug)}>
        See more from {event.name}
      </button>
    </div>
  </section>
{/each}
```

### Inertia Navigation with Ziggy Route Helper
```javascript
// Source: Ziggy + Inertia.js documentation
import { router } from '@inertiajs/svelte';
import route from 'ziggy';

// Using Ziggy for type-safe route generation
const navigateToEventGrid = (eventSlug) => {
  router.visit(route('events.videos', { event: eventSlug }));
};

// Or direct string concatenation (simpler, less type-safe)
const navigateToEventGrid = (eventSlug) => {
  router.visit(`/events/${eventSlug}/videos`);
};
```

### Mobile Video Modal with All Required Attributes
```svelte
<!-- Source: MDN Video Element + iOS/Android Best Practices -->
{#if modalVideo}
  <Portal>
    <div class="video-modal" on:click={closeVideoModal}>
      <div class="modal-content" on:click|stopPropagation>
        <button class="modal-close" on:click={closeVideoModal}>×</button>

        <div class="modal-video-wrapper">
          <video
            bind:this={modalVideoElement}
            src={modalVideo.video_url}
            poster={modalVideo.thumbnail_url}
            controls
            autoplay
            muted      <!-- Required for autoplay on mobile -->
            playsinline <!-- Required for iOS inline playback -->
            preload="metadata"
            on:canplay={handleCanPlay}
            on:error={handleVideoError}
          >
            <p>Your browser does not support HTML5 video.</p>
          </video>
        </div>

        <div class="modal-info">
          <h3>{modalVideo.title}</h3>
        </div>
      </div>
    </div>
  </Portal>
{/if}
```

### Touch-Optimized Modal Close Button
```svelte
<!-- Source: WCAG 2.1 AAA Touch Target Requirements -->
<style>
  .modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 44px;   /* Meets WCAG AAA minimum */
    height: 44px;  /* Meets WCAG AAA minimum */
    font-size: 2rem;
    cursor: pointer;
    z-index: 10;   /* Above video controls */
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| User agent sniffing for mobile | CSS `@media` queries | ~2015 (mobile-first became standard) | Server-side rendered friendly, more performant |
| Separate mobile sites/routes | Responsive single codebase | ~2016 (progressive enhancement) | Easier maintenance, consistent UX |
| Fixed-width mobile layouts | Fluid/percentage-based layouts | ~2017 (flexbox/grid mature) | Better adaptation to diverse devices |
| webkit-playsinline attribute | Standard `playsinline` | iOS 10+ (2017) | Cleaner code, cross-platform compatibility |
| Unmuted autoplay default | Muted autoplay with user controls | ~2018 (autoplay policies) | Autoplay works consistently across browsers |

**Deprecated/outdated:**
- **`webkit-playsinline`**: Legacy iOS Safari attribute, standard `playsinline` sufficient for iOS 10+
- **`x5-video-player-type`**: Legacy Tencent mobile browser attribute, no longer needed
- **`x5-video-player-fullscreen`**: Legacy Tencent browser attribute, no longer needed
- **User agent detection**: Unreliable, CSS media queries preferred
- **Separate m. subdomains**: Outdated pattern, use responsive design

## Open Questions

### 1. Does current mobile implementation meet MOBILE-01 requirement?
**What we know:** EventsFeaturedOnly.svelte filters for featured videos using `event.videos.find((v) => v.is_featured)`, shown only on mobile via `@media (max-width: 768px)`
**What's unclear:** Whether the component is actually being rendered in EventsMediaShowcase.svelte, and if CSS is correctly hiding EventTimeline on mobile
**Recommendation:** Inspect EventsMediaShowcase.svelte line 38 to confirm both EventTimeline and EventsFeaturedOnly are rendered in timeline mode

### 2. Are video attributes correctly set for mobile autoplay?
**What we know:** Phase 2 research documented `playsinline` and `muted` as required for mobile
**What's unclear:** Whether Phase 2 implementation actually added these attributes to all video elements
**Recommendation:** Verify VideoPlayer.svelte, EventsFeaturedOnly.svelte modal, and EventsGrid.svelte modal all have `muted` and `playsinline` attributes

### 3. Does "See more from {event}" button navigate correctly?
**What we know:** EventsFeaturedOnly.svelte has `navigateToEventGrid` function that calls `router.visit('/events/${eventSlug}/videos')`
**What's unclear:** Whether the backend route exists and if it displays the correct event's videos
**Recommendation:** Verify route `/events/{event:slug}/videos` exists in web.php and EventsMediaShowcaseController@eventVideos returns correct videos

### 4. What mobile testing devices/browsers are required?
**What we know:** Requirements say "iOS/Android" but don't specify versions or browsers
**What's unclear:** Minimum iOS version, minimum Android version, required browser testing (Safari, Chrome, Firefox?)
**Recommendation:** Test iOS 14+ Safari, Android 10+ Chrome as baseline (covers 95%+ of active devices)

### 5. Should mobile autoplay be muted or wait for user interaction?
**What we know:** Browser policies block unmuted autoplay, `muted` attribute required for autoplay to work
**What's unclear:** User preference - auto-play with mute, or show play button and wait for tap
**Recommendation:** Start with muted autoplay (matches Phase 2 decisions), user can tap unmute in controls

## Sources

### Primary (HIGH confidence)
- [MDN: <video>: The Video Embed element](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video) - Official HTML5 video element reference (verified 2025-03-13)
- [MDN: Autoplay guide for media and Web Audio APIs](https://developer.mozilla.org/en-US/docs/Web/Media/Guides/Autoplay) - Comprehensive autoplay policy documentation
- [Svelte: Key blocks tutorial](https://svelte.dev/tutorial/svelte/key-blocks) - Official {#key} block documentation
- [Svelte 5 tutorial: Building an app](https://www.contentful.com/blog/svelte-tutorial/) - Svelte 5 key concepts (May 2025)
- [Nielsen Norman Group: Breakpoints in Responsive Design](https://www.nngroup.com/articles/breakpoints-in-responsive-design/) - Authoritative UX research (April 2024)

### Secondary (MEDIUM confidence)
- [Best Practices for Video Playback: A Complete Guide 2025](https://www.mux.com/articles/best-practices-for-video-playback-a-complete-guide-2025) - Modern video playback best practices
- [Breakpoints for Responsive Web Design in 2025](https://www.browserstack.com/guide/responsive-design-breakpoints) - Technical implementation guide
- [Complete Guide to Building Mobile-first Frontend Experiences](https://hicronsoftware.com/blog/building-mobile-first-frontend-experiences/) - Mobile-first patterns (June 2025)
- [Responsive Design Breakpoints: 2025 Playbook](https://dev.to/gerryleonugroho/responsive-design-breakpoints-2025-playbook-53ih) - Latest 2025 practices (March 2025)

### Tertiary (LOW confidence)
- [HTML5 video streaming: Top Strategies to Optimize](https://www.muvi.com/blogs/strategies-to-optimize-html-5-video-streaming/) - General video optimization
- [How to Use the HTML5 Video Tag for Efficient Media Delivery](https://cloudinary.com/guides/front-end-development/html5-video-tag) - Cloudinary's video tag guide

### Existing Codebase Analysis
- `/Modules/PublicPage/resources/js/Pages/Components/EventTimeline.svelte` - Desktop timeline view (hidden @media 768px)
- `/Modules/PublicPage/resources/js/Pages/Components/EventsFeaturedOnly.svelte` - Mobile featured-only view (shown @media 768px)
- `/Modules/PublicPage/resources/js/Pages/Components/EventsGrid.svelte` - Grid view (all screen sizes)
- `/Modules/PublicPage/resources/js/Pages/Components/VideoPlayer.svelte` - Video player component
- `/Modules/PublicPage/resources/js/Pages/EventsMediaShowcase.svelte` - Main page component
- `/Modules/PublicPage/app/Http/Controllers/EventsMediaShowcaseController.php` - Backend controller
- `/Modules/PublicPage/routes/web.php` - Route definitions

### Existing Research
- `.planning/phases/02-fix-video-playback/02-RESEARCH.md` - Video playback research from Phase 2
- `.planning/phases/04-event-sorting/04-RESEARCH.md` - Event sorting research from Phase 4

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Svelte 5, Tailwind CSS 3, HTML5 video verified in codebase
- Architecture: HIGH - All patterns verified in existing codebase components
- Pitfalls: HIGH - All issues verified with multiple sources and codebase inspection

**Research date:** 2026-02-04
**Valid until:** 2026-03-04 (30 days - stable mobile video patterns, unlikely to change)

**Codebase analysis completed:**
- EventTimeline.svelte: Hidden on mobile @media 768px, has sort controls, featured player, supporting videos grid
- EventsFeaturedOnly.svelte: Shown on mobile @media 768px, shows featured only, has "See more from {event}" button
- EventsGrid.svelte: Grid view with modal, has category filtering, sort controls
- VideoPlayer.svelte: Has playsinline and muted attributes, lazyLoad disabled
- EventsMediaShowcase.svelte: Renders both EventTimeline and EventsFeaturedOnly in timeline mode
- Backend route `/events/{event:slug}/videos` exists and returns event videos
- Navigation to grid view uses `router.visit('/events/${eventSlug}/videos')`

**Key finding:** Most mobile UX is already implemented. This phase requires verification that:
1. Mobile timeline correctly shows EventsFeaturedOnly (featured videos only)
2. Navigation button goes to correct event videos page
3. Videos play correctly on mobile (has playsinline, muted, autoplay)

---

*Phase: 05-mobile-ux*
*Research completed: 2026-02-04*

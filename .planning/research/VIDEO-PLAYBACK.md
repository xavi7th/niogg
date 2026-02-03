# HTML5 Video Playback Issues

**Research Date:** 2026-02-02

## Key Findings

### 1. Modal Video Playback

**Issue:** Videos in modals often need a second click to play.

**Solution:** Wait for modal transition, then call play():

```javascript
setTimeout(() => {
  modalVideoElement.play();
}, 300);
```

### 2. Svelte bind:this Patterns

```svelte
<script>
  let videoElement;

  function playVideo() {
    videoElement.play();
  }
</script>

<video bind:this={videoElement} />
```

### 3. Source Loading Timing

Wait for `loadeddata` or `canplay` events:

```svelte
<video
  on:canplay={() => videoElement.play()}
  preload="metadata"
/>
```

### 4. Mobile Video Requirements

**Required attributes:**

- `playsinline` - REQUIRED for iOS
- `muted` - for autoplay to work
- `webkit-playsinline` - older iOS fallback

### 5. Autoplay Policies

Chrome/Safari require `muted` for autoplay. User interaction required before autoplay with sound.

---

## Action Items for This Project

1. Add `playsinline` attribute to all video elements
2. Ensure modal videos have proper timing (setTimeout or event-based)
3. Handle play() Promise rejection for autoplay policy compliance
4. Test on real mobile devices

---

_Sources: MDN, Stack Overflow, Svelte docs_

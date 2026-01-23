<script>
  import { onMount, onDestroy } from 'svelte';

  let videoElement;
  let isVisible = false;
  let observer;
  let hasClickedPlay = false;

  export let video = {};
  export let isFeatured = false;

  onMount(() => {
    // Intersection Observer for lazy loading
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          isVisible = true;
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '200px 0px', // Load 200px before entering viewport
      threshold: 0.01
    });

    if (videoElement) {
      observer.observe(videoElement);
    }
  });

  onDestroy(() => {
    if (observer) {
      observer.disconnect();
    }
  });

  function handlePlay() {
    hasClickedPlay = true;
  }

  function handlePause() {
    // Optional: Handle pause if needed
  }

  function handlePlayClick() {
    if (videoElement) {
      videoElement.play();
      hasClickedPlay = true;
    }
  }
</script>

{#if isVisible}
  <div class="lazy-video-container">
    <video
      bind:this={videoElement}
      poster={video.thumbnail_url}
      controls
      class="lazy-video-element"
      preload="metadata"
      on:play={handlePlay}
      on:pause={handlePause}
      style="will-change: transform;"
    >
      <source src={video.video_url} type="video/mp4" />
      <p>Your browser does not support HTML5 video.</p>
    </video>

    {#if !hasClickedPlay}
      <div class="video-overlay" on:click={handlePlayClick}>
        <img
          src={video.thumbnail_url}
          alt={video.title}
          class="overlay-thumbnail"
          loading="lazy"
        />
        {#if isFeatured}
          <span class="featured-badge">Featured</span>
        {/if}
        <div class="play-button-overlay">
          <svg
            width="100"
            height="100"
            viewBox="0 0 100 100"
            fill="#ff7607"
            class="play-icon"
            aria-hidden="true"
          >
            <circle cx="50" cy="50" r="48" fill="none" stroke="#ff7607" stroke-width="2" />
            <polygon points="35,20 35,80 80,50" fill="#ff7607" />
          </svg>
        </div>
      </div>
    {/if}
  </div>
{:else}
  <div class="video-loading-placeholder">
    <img
      src={video.thumbnail_url}
      alt={video.title}
      class="placeholder-thumbnail"
      loading="lazy"
    />
    <div class="loading-indicator">
      <div class="loading-spinner"></div>
      <span class="loading-text">Tap to load video</span>
    </div>
  </div>
{/if}

<style>
  .lazy-video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .lazy-video-element {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s ease;
  }

  .overlay-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .play-button-overlay {
    position: relative;
    z-index: 3;
    transition: transform 0.2s ease;
  }

  .play-button-overlay:hover {
    transform: scale(1.05);
  }

  .video-overlay:hover .play-button-overlay {
    transform: scale(1.1);
  }

  .featured-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background-color: #ff7607;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 2;
  }

  .video-loading-placeholder {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #1a1a1a;
    border-radius: 4px;
    overflow: hidden;
  }

  .placeholder-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.7;
  }

  .loading-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 2;
  }

  .loading-spinner {
    width: 48px;
    height: 48px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #ff7607;
    animation: spin 1s linear infinite;
    margin-bottom: 0.75rem;
  }

  .loading-text {
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  /* Performance optimizations */
  .lazy-video-container,
  .video-loading-placeholder {
    will-change: transform;
    transform: translateZ(0);
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    .loading-spinner {
      animation: none;
    }
  }

  /* Mobile touch optimizations */
  @media (hover: none) and (pointer: coarse) {
    .play-button-overlay:active {
      transform: scale(0.95);
    }

    .video-overlay:active {
      opacity: 0.9;
    }
  }
</style>
<script>
  let isPlaying = false;
  let videoElement;
  let isLoaded = false;

  export let video = {};
  export let size = 'large';
  export let lazyLoad = false; // Disabled for testing

  const isBig = size === 'large';
  const playButtonSize = isBig ? 100 : 50;
  const titleSize = isBig ? '1.75rem' : '1.125rem';

  // Initialize as loaded for testing
  isLoaded = true;

  function handlePlayClick() {
    if (videoElement && isLoaded) {
      videoElement.play();
    }
  }

  function handlePlay() {
    isPlaying = true;
  }

  function handlePause() {
    isPlaying = false;
  }
</script>

<div class="video-player" class:large={isBig} class:small={!isBig}>
  <div class="video-container">
    {#if isLoaded}
      <video
        bind:this={videoElement}
        poster={video.thumbnail_url}
        controls
        class="video-element"
        preload="metadata"
        on:play={handlePlay}
        on:pause={handlePause}
      >
        <source src={video.video_url} type="video/mp4" />
        <p>Your browser does not support HTML5 video.</p>
      </video>
    {:else}
      <div class="video-placeholder">
        <img src={video.thumbnail_url} alt={video.title} class="placeholder-image" />
        <div class="loading-overlay">
          <div class="loading-spinner"></div>
          <span class="loading-text">Tap to load</span>
        </div>
        <button
          class="play-button-placeholder"
          on:click={handlePlayClick}
          role="button"
          tabindex="0"
        >
          <svg
            width={playButtonSize}
            height={playButtonSize}
            viewBox="0 0 100 100"
            fill="#ff7607"
            class="play-icon"
            aria-hidden="true"
          >
            <circle cx="50" cy="50" r="48" fill="none" stroke="#ff7607" stroke-width="2" />
            <polygon points="35,20 35,80 80,50" fill="#ff7607" />
          </svg>
        </button>
      </div>
    {/if}

    {#if !isPlaying}
      <div class="play-button-overlay" on:click={handlePlayClick} role="button" tabindex="0">
        <svg
          width={playButtonSize}
          height={playButtonSize}
          viewBox="0 0 100 100"
          fill="#ff7607"
          class="play-icon"
          aria-hidden="true"
        >
          <circle cx="50" cy="50" r="48" fill="none" stroke="#ff7607" stroke-width="2" />
          <polygon points="35,20 35,80 80,50" fill="#ff7607" />
        </svg>
      </div>
    {/if}

    <div class="metadata-overlay">
      {#if video.is_featured}
        <span class="featured-badge">Featured</span>
      {/if}
      <h3 class="video-title">{video.title}</h3>
      <div class="video-meta">
        <span class="duration">{video.formatDuration || '0:00'}</span>
      </div>
    </div>
  </div>
</div>

<style>
  .video-player {
    position: relative;
    width: 100%;
  }

  .video-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
  }

  .video-placeholder {
    position: relative;
    width: 100%;
    height: 100%;
    background: #000;
    border-radius: 4px;
    overflow: hidden;
  }

  .placeholder-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
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

  .play-button-placeholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 118, 7, 0.8);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    z-index: 3;
    transition: all 0.2s ease;
  }

  .play-button-placeholder:hover {
    background: rgba(255, 118, 7, 1);
    transform: translate(-50%, -50%) scale(1.1);
  }

  .play-button-placeholder:active {
    transform: translate(-50%, -50%) scale(0.95);
  }

  .video-element {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .play-button-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    cursor: pointer;
    z-index: 2;
    transition: transform 0.2s ease;
  }

  .play-button-overlay:hover .play-icon {
    transform: scale(1.1);
  }

  .play-icon {
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
    transition: transform 0.2s ease;
  }

  .video-element::-webkit-media-controls-play-button {
    display: none;
  }

  .metadata-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
    padding: 1.5rem 1rem;
    color: white;
    z-index: 3;
  }

  .featured-badge {
    display: inline-block;
    background-color: #ff7607;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .video-title {
    margin: 0 0 0.5rem 0;
    font-size: clamp(1rem, 4vw, 1.75rem);
    font-weight: 700;
  }

  .video-meta {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    color: rgba(255, 255, 255, 0.8);
  }

  .duration {
    margin-right: 1rem;
  }

  .play-button-overlay {
    min-width: 48px;
    min-height: 48px;
  }

  @media (max-width: 768px) {
    .large {
      --play-button-size: 60px;
    }

    .metadata-overlay {
      padding: 1rem 0.75rem;
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .play-button-overlay:active {
        transform: scale(0.95);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      }

      .play-button-overlay {
        -webkit-tap-highlight-color: transparent;
      }
    }

    /* Enhanced touch targets */
    .play-button-overlay {
      min-height: 72px;
      min-width: 72px;
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .play-button-overlay {
        transition: none !important;
      }

      .metadata-overlay {
        transition: none !important;
      }
    }
  }
</style>

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

  // Detect video MIME type based on file extension
  $: videoMimeType = (() => {
    if (!video?.video_url) return 'video/mp4';
    try {
      const urlParts = video.video_url.split('.');
      if (urlParts.length < 2) return 'video/mp4';
      const extension = urlParts.pop().toLowerCase();
      switch (extension) {
        case 'mp4': return 'video/mp4';
        case 'webm': return 'video/webm';
        case 'mov': return 'video/quicktime';
        default: return 'video/mp4';
      }
    } catch {
      return 'video/mp4';
    }
  })();

  function handlePlayClick() {
    if (videoElement && isLoaded && video?.video_url) {
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
        <source src={video.video_url} type={videoMimeType} />
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
            class="play-icon"
            aria-hidden="true"
          >
            <circle cx="50" cy="50" r="48" fill="white" />
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
          class="play-icon"
          aria-hidden="true"
        >
          <circle cx="50" cy="50" r="48" fill="#222222" />
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
        <span class="duration">⏱ {video.formatDuration || '0:00'}</span>
        <span class="date">📅 {video.date || ''}</span>
        <span class="views">👁 {video.views || 0}</span>
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
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 20px 100px 0px rgba(40, 40, 40, 0.15);
  }

  .video-placeholder {
    position: relative;
    width: 100%;
    height: 100%;
    background: #000;
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
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    z-index: 1;
  }

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #ff7607;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .loading-text {
    color: white;
    font-size: 0.875rem;
  }

  .play-button-placeholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    margin: 0;
  }

  .play-button-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #222;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .play-button-overlay:hover {
    background: rgba(0, 0, 0, 0.9);
  }

  .play-icon {
    background: #ff7607;
    border-radius: 50%;
    padding: 10px;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
    transition: all 0.2s ease;
  }

  .play-button-overlay:hover .play-icon {
    background: rgba(255, 255, 255, 0.9);
    transform: scale(1.1);
  }

  .play-button-overlay:hover .play-icon polygon {
    fill: #ff7607;
  }

  .play-icon polygon {
    fill: white;
  }

  .video-element {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .metadata-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 40%, rgba(27, 26, 26, 0.7));
    color: white;
    padding: 30px;
    z-index: 3;
  }

  .featured-badge {
    display: inline-block;
    background-color: rgba(255, 118, 7, 0.95);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.75rem;
  }

  .video-title {
    font-weight: 500;
    line-height: 1.3;
    font-size: 1.75rem;
    margin: 0 0 0.5rem 0;
    color: rgba(255, 255, 255, 0.8);
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  }

  .video-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .duration {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .views {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .small .video-title {
    font-size: 1.125rem;
  }

  @media (max-width: 768px) {
    .metadata-overlay {
      padding: 20px;
    }

    .video-title {
      font-size: 1.125rem;
    }
  }
</style>

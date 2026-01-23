<script>
  export let video = {};
  export let size = 'large';

  const isBig = size === 'large';
  const playButtonSize = isBig ? 100 : 50;
  const titleSize = isBig ? '1.75rem' : '1.125rem';
</script>

<div class="video-player" class:large={isBig} class:small={!isBig}>
  <div class="video-container">
    <video
      poster={video.thumbnail_url}
      controls
      class="video-element"
      preload="metadata"
    >
      <source src={video.video_url} type="video/mp4" />
      <p>Your browser does not support HTML5 video.</p>
    </video>

    <div class="play-button-overlay">
      <svg
        width={playButtonSize}
        height={playButtonSize}
        viewBox="0 0 100 100"
        fill="#ff7607"
        class="play-icon"
      >
        <circle cx="50" cy="50" r="48" fill="none" stroke="#ff7607" stroke-width="2" />
        <polygon points="35,20 35,80 80,50" fill="#ff7607" />
      </svg>
    </div>

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
    pointer-events: none;
    z-index: 2;
    transition: transform 0.2s ease;
  }

  .play-icon {
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
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
    font-size: v-bind('titleSize');
    font-weight: 700;
  }

  .video-meta {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
  }

  .duration {
    margin-right: 1rem;
  }

  @media (max-width: 768px) {
    .large {
      --play-button-size: 60px;
    }
  }
</style>

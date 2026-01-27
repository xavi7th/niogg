<script>
  import LazyThumbnail from './LazyThumbnail.svelte';

  export let videos = [];
  export let currentlyPlaying = null;
  export let onVideoSelect = () => {};

  const getCategoryLabel = (category) => {
    if (!category) return '';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };
</script>

<div class="section-title">
  <h2>More from this event</h2>
</div>

<div class="supporting-grid">
  {#each videos as video (video.id)}
    <div
      class="video-card"
      class:active={video.id === currentlyPlaying}
      on:click={() => onVideoSelect(video)}
      role="button"
      tabindex="0"
      on:keydown={(e) => e.key === 'Enter' && onVideoSelect(video)}
    >
      <div class="card-image-container">
        <LazyThumbnail
          src={video.thumbnail_url}
          alt={video.title}
          placeholder="/images/video-placeholder-default.jpg"
          class="card-thumbnail"
        />

        <div class="gradient-overlay"></div>

        {#if video.formatDuration}
          <div class="duration-badge-thumbnail">
            {video.formatDuration}
          </div>
        {/if}

        <div class="play-button">
          <svg width="50" height="50" viewBox="0 0 100 100" fill="none">
            <polygon points="35,20 35,80 80,50" fill="currentColor" />
          </svg>
        </div>
      </div>

      <div class="card-content">
        {#if video.date}
          <div class="card-date">{video.date}</div>
        {/if}
        <h4 class="card-title">{video.title}</h4>
        <div class="card-badges">
          {#if video.category}
            <span class="category-badge">{getCategoryLabel(video.category)}</span>
          {/if}
        </div>
      </div>
    </div>
  {/each}
</div>

<style>
  .section-title {
    margin: 40px 0 30px 0;
    border-left: 4px solid #ff7607;
    padding-left: 16px;
  }

  .section-title h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1b1a1a;
    margin: 0;
  }

  .supporting-grid {
    display: flex;
    flex-wrap: nowrap;
    gap: 1.875rem;
    margin: 0 0 2rem 0;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    padding-bottom: 1rem;
  }

  .supporting-grid::-webkit-scrollbar {
    height: 8px;
  }

  .supporting-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }

  .supporting-grid::-webkit-scrollbar-thumb {
    background: #ff7607;
    border-radius: 10px;
  }

  .supporting-grid::-webkit-scrollbar-thumb:hover {
    background: #e66d06;
  }

  .video-card {
    cursor: pointer;
    transition: all 0.3s ease;
    background: #ffffff;
    border-radius: 6px;
    overflow: hidden;
    flex: 0 0 calc(25% - 1.40625rem);
    min-width: 250px;
    box-shadow: 0px 5px 40px 0px rgba(40, 40, 40, 0.08);
  }

  .video-card:hover {
    box-shadow: 0px 15px 60px 0px rgba(40, 40, 40, 0.15);
  }

  .video-card:hover {
    transform: translateY(-8px);
  }

  .video-card.active {
    background-color: rgba(255, 118, 7, 0.05);
    border: 3px solid #ff7607;
  }

  .video-card:not(.active) {
    border: 3px solid transparent;
  }

  .card-image-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #f0f0f0;
    overflow: hidden;
  }

  .card-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
    will-change: transform;
    transform: translateZ(0);
  }

  .card-thumbnail:hover {
    transform: scale(1.1);
  }

  .gradient-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0), rgba(27, 26, 26, 0.5));
    pointer-events: none;
  }

  .duration-badge-thumbnail {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background-color: rgba(27, 26, 26, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    pointer-events: none;
  }

  .play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background-color: rgba(255, 118, 7, 0.95);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.2s ease;
    z-index: 2;
  }

  .video-card:hover .play-button {
    background-color: white;
    color: #ff7607;
    transform: translate(-50%, -50%) scale(1.15);
  }

  .card-content {
    padding: 1rem;
  }

  .card-date {
    font-size: 13px;
    color: #9b9b9b;
    margin-bottom: 0.5rem;
  }

  .card-title {
    margin: 0 0 0.5rem 0;
    font-size: 16px;
    font-weight: 700;
    color: #1b1a1a;
    line-height: 1.4;
    transition: color 0.2s ease;
  }

  .card-title:hover {
    color: #ff7607;
  }

  .card-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.75rem;
  }

  .category-badge {
    background-color: #f0f0f0;
    color: #1b1a1a;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
  }

  @media (max-width: 991px) {
    .supporting-grid {
      flex-wrap: wrap;
      overflow-x: visible;
    }

    .video-card {
      flex: 0 0 calc(50% - 0.9375rem);
      min-width: auto;
    }
  }

  @media (max-width: 768px) {
    .supporting-grid {
      flex-wrap: wrap;
      overflow-x: visible;
    }

    .video-card {
      flex: 0 0 100%;
      min-width: auto;
    }
  }
</style>

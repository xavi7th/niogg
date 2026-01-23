<script>
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
        <img src={video.thumbnail_url} alt={video.title} loading="lazy" class="card-image" />

        <div class="play-button">
          <svg width="50" height="50" viewBox="0 0 100 100" fill="currentColor">
            <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="2" />
            <polygon points="35,20 35,80 80,50" fill="currentColor" />
          </svg>
        </div>
      </div>

      <div class="card-content">
        <h4 class="card-title">{video.title}</h4>
        <div class="card-badges">
          {#if video.category}
            <span class="category-badge">{getCategoryLabel(video.category)}</span>
          {/if}
          {#if video.formatDuration}
            <span class="duration-badge">{video.formatDuration}</span>
          {/if}
        </div>
      </div>
    </div>
  {/each}
</div>

<style>
  .supporting-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.875rem;
    margin: 2rem 0;
  }

  .video-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 4px;
    overflow: hidden;
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

  .card-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
  }

  .video-card:hover .card-image {
    transform: scale(1.1);
  }

  .play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background-color: rgba(255, 118, 7, 0.8);
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

  .card-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #1b1a1a;
    line-height: 1.4;
  }

  .card-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    font-size: 0.75rem;
  }

  .category-badge,
  .duration-badge {
    background-color: #f0f0f0;
    color: #1b1a1a;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
  }

  @media (max-width: 991px) {
    .supporting-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .supporting-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

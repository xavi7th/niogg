<script>
  export let events = [];
  export let onBackToTimeline = () => {};

  let selectedCategory = 'all';

  const categories = Array.from(
    new Set(events.flatMap((e) => e.videos?.map((v) => v.category || e.category) || []))
  ).filter(Boolean);

  const getFilteredVideos = () => {
    if (selectedCategory === 'all') {
      return events.flatMap((e) => e.videos || []);
    }
    return events.flatMap((e) =>
      (e.videos || []).filter((v) => (v.category || e.category) === selectedCategory)
    );
  };

  const getCategoryLabel = (category) => {
    if (!category) return '';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  $: filteredVideos = getFilteredVideos();
</script>

<div class="events-grid-view">
  <div class="grid-header">
    <button on:click={onBackToTimeline} class="btn-back">← Back to Timeline</button>
  </div>

  <div class="filter-tabs">
    <button
      class="tab-btn"
      class:active={selectedCategory === 'all'}
      on:click={() => (selectedCategory = 'all')}
    >
      All Events
    </button>
    {#each categories as category (category)}
      <button
        class="tab-btn"
        class:active={selectedCategory === category}
        on:click={() => (selectedCategory = category)}
      >
        {getCategoryLabel(category)}
      </button>
    {/each}
  </div>

  <div class="video-grid">
    {#each filteredVideos as video (video.id)}
      <div class="video-card">
        <div class="card-image">
          <img src={video.thumbnail_url} alt={video.title} loading="lazy" />
          <div class="play-button">
            <svg width="50" height="50" viewBox="0 0 100 100" fill="currentColor">
              <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="2" />
              <polygon points="35,20 35,80 80,50" fill="currentColor" />
            </svg>
          </div>
        </div>

        <div class="card-content">
          <h4 class="card-title">{video.title}</h4>
          <div class="card-info">
            {#if video.formatDuration}
              <span class="duration">{video.formatDuration}</span>
            {/if}
            {#if video.category}
              <span class="category">{getCategoryLabel(video.category)}</span>
            {/if}
          </div>
        </div>
      </div>
    {/each}
  </div>

  {#if filteredVideos.length === 0}
    <div class="empty-state">
      <p>No videos found for this category.</p>
    </div>
  {/if}

  <div class="load-more">
    <button class="btn-load-more">Load More</button>
  </div>
</div>

<style>
  .events-grid-view {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  .grid-header {
    margin-bottom: 2rem;
  }

  .btn-back {
    background-color: transparent;
    color: #ff7607;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
  }

  .btn-back:hover {
    color: #e66d06;
  }

  .filter-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
  }

  .tab-btn {
    background-color: transparent;
    color: #9b9b9b;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
  }

  .tab-btn.active {
    color: #ff7607;
    border-bottom-color: #ff7607;
  }

  .tab-btn:hover {
    color: #1b1a1a;
  }

  .video-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.875rem;
    margin: 2rem 0;
  }

  .video-card {
    border-radius: 4px;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .video-card:hover {
    transform: translateY(-4px);
  }

  .card-image {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #f0f0f0;
    overflow: hidden;
  }

  .card-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
  }

  .video-card:hover .card-image img {
    transform: scale(1.05);
  }

  .play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background-color: rgba(255, 118, 7, 0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    z-index: 2;
  }

  .card-content {
    padding: 1rem;
  }

  .card-title {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 700;
    color: #1b1a1a;
  }

  .card-info {
    font-size: 0.875rem;
    color: #9b9b9b;
    display: flex;
    gap: 1rem;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9b9b9b;
  }

  .load-more {
    text-align: center;
    padding: 2rem 1rem;
  }

  .btn-load-more {
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .btn-load-more:hover {
    background-color: #e66d06;
  }

  @media (max-width: 991px) {
    .video-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .video-grid {
      grid-template-columns: 1fr;
    }

    .events-grid-view {
      padding: 1rem 0.75rem;
    }
  }
</style>

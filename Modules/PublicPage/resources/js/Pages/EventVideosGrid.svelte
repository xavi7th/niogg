<script>
  import { Link } from '@inertiajs/svelte';
  import PublicPageLayout from '@publicpage-pages/Layouts/PublicPageLayout.svelte';

  export let event = {};
  export let videos = [];
  export let pageTitle = 'Event Videos';

  const getCategoryLabel = (category) => {
    if (!category) return '';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };
</script>

<PublicPageLayout {pageTitle}>
  <div class="event-videos-grid">
    <div class="page-header">
      <nav class="breadcrumb">
        <Link href="/">Home</Link>
        <span class="separator">/</span>
        <Link href={route('events.media-showcase')}>Events</Link>
        <span class="separator">/</span>
        <Link href={route('events.media-showcase')}>Media Showcase</Link>
        <span class="separator">/</span>
        <span>{event.name}</span>
      </nav>

      <h1 class="page-title">{event.name}</h1>

      <Link href={route('events.media-showcase')} class="btn-back">
        ← Back to Media Showcase
      </Link>
    </div>

    <div class="video-grid">
      {#each videos.data || videos as video (video.id)}
        <div class="video-card">
          <div class="card-image">
            <img src={video.thumbnail_url} alt={video.title} loading="lazy" />
            <div class="play-button">
              <svg width="50" height="50" viewBox="0 0 100 100" fill="currentColor">
                <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="2" />
                <polygon points="35,20 35,80 80,50" fill="currentColor" />
              </svg>
            </div>
            {#if video.is_featured}
              <span class="featured-badge">Featured</span>
            {/if}
          </div>

          <div class="card-content">
            <h4 class="card-title">{video.title}</h4>
            <div class="card-info">
              {#if video.format_duration}
                <span class="duration">{video.format_duration}</span>
              {/if}
              {#if event.category}
                <span class="category">{getCategoryLabel(event.category)}</span>
              {/if}
            </div>
          </div>
        </div>
      {/each}
    </div>

    {#if videos.links && videos.next_page_url}
      <div class="load-more">
        <button class="btn-load-more">Load More</button>
      </div>
    {/if}
  </div>
</PublicPageLayout>

<style>
  .event-videos-grid {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
  }

  .page-header {
    margin-bottom: 3rem;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: #9b9b9b;
  }

  .breadcrumb a {
    color: #ff7607;
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .breadcrumb a:hover {
    color: #e66d06;
  }

  .separator {
    margin: 0 0.25rem;
  }

  .page-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1b1a1a;
    margin: 1rem 0;
  }

  .btn-back {
    display: inline-block;
    background-color: transparent;
    color: #ff7607;
    border: none;
    font-size: 1rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
    text-decoration: none;
  }

  .btn-back:hover {
    color: #e66d06;
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

  .featured-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    background-color: #ff7607;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
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

    .page-title {
      font-size: 1.5rem;
    }

    .event-videos-grid {
      padding: 1rem 0.75rem;
    }
  }
</style>

<script>
  import { Link } from '@inertiajs/svelte';
  import PublicPageLayout from '@publicpage-pages/Layouts/PublicPageLayout.svelte';
  import LazyThumbnail from '@publicpage-pages/Components/LazyThumbnail.svelte';

  export let event = {};
  export let videos = [];
  export let pageTitle = 'Event Videos';

  let modalVideo = null;
  let modalVideoElement = null;
  let playAttempted = false;

  const getCategoryLabel = (category) => {
    if (!category) return '';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  const openVideoModal = (video) => {
    modalVideo = video;
    playAttempted = false;
  };

  const handleCanPlay = () => {
    if (!playAttempted && modalVideoElement) {
      playAttempted = true;
      modalVideoElement.play().catch(error => {
        console.log('Autoplay prevented:', error.name);
        // User will need to click play manually - browser policy
      });
    }
  };

  const closeVideoModal = () => {
    if (modalVideoElement) {
      modalVideoElement.pause();
      modalVideoElement.currentTime = 0;
      modalVideoElement.src = '';
    }
    modalVideo = null;
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
        <div class="video-card" on:click={() => openVideoModal(video)} role="button" tabindex="0">
          <div class="card-image">
            <LazyThumbnail
              src={video.thumbnail_url}
              alt={video.title}
              placeholder="/images/video-placeholder-default.jpg"
              class="card-thumbnail"
            />
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

  <!-- Video Modal -->
  {#if modalVideo}
    <div class="video-modal" on:click={closeVideoModal} on:keydown={(e) => e.key === 'Escape' && closeVideoModal()} role="dialog" aria-modal="true">
      <div class="modal-content" on:click|stopPropagation>
        <button class="modal-close" on:click={closeVideoModal} aria-label="Close modal">×</button>
        <div class="modal-video-wrapper">
          <video
            bind:this={modalVideoElement}
            src={modalVideo.video_url}
            poster={modalVideo.thumbnail_url}
            controls
            on:canplay={handleCanPlay}
            preload="metadata"
            class="modal-video-element"
          >
            <p>Your browser does not support HTML5 video.</p>
          </video>
        </div>
        <div class="modal-info">
          <h3 class="modal-title">{modalVideo.title}</h3>
          {#if modalVideo.description}
            <p class="modal-description">{modalVideo.description}</p>
          {/if}
        </div>
      </div>
    </div>
  {/if}
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
    font-size: clamp(0.75rem, 1.5vw, 0.875rem);
    color: #9b9b9b;
    flex-wrap: wrap;
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
    font-size: clamp(1.5rem, 5vw, 2.25rem);
    font-weight: 700;
    color: #1b1a1a;
    margin: 1rem 0;
  }

  .btn-back {
    display: inline-flex;
    align-items: center;
    background-color: transparent;
    color: #ff7607;
    border: none;
    font-size: clamp(0.875rem, 2vw, 1rem);
    cursor: pointer;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
    text-decoration: none;
    min-height: 48px;
    min-width: 48px;
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
    cursor: pointer;
  }

  .video-card:hover {
    transform: translateY(-4px);
  }

  .video-card:focus-visible {
    outline: 2px solid #ff7607;
    outline-offset: 2px;
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
    transition: transform 0.2s ease;
  }

  .video-card:hover .play-button {
    transform: translate(-50%, -50%) scale(1.1);
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
    font-size: clamp(0.875rem, 2vw, 1rem);
    font-weight: 700;
    color: #1b1a1a;
  }

  .card-info {
    font-size: clamp(0.75rem, 1.5vw, 0.875rem);
    color: #9b9b9b;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
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
    font-size: clamp(0.875rem, 2vw, 1rem);
    cursor: pointer;
    transition: background-color 0.2s ease;
    min-height: 48px;
    min-width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-load-more:hover {
    background-color: #e66d06;
  }

  /* Video Modal */
  .video-modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0, 0, 0, 0.9);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    overflow: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-y;
    min-height: 100vh;
    min-height: 100dvh;
    width: 100vw;
    max-width: 100vw;
  }

  .modal-content {
    background: white;
    border-radius: 8px;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
  }

  .modal-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    font-size: 2rem;
    line-height: 1;
    width: 44px;
    height: 44px;
    cursor: pointer;
    z-index: 10;
    border-radius: 4px;
    transition: background 0.2s ease;
  }

  .modal-close:hover {
    background: #f0f0f0;
  }

  .modal-video-wrapper {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%;
    background: #000;
  }

  .modal-video-element {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  .modal-info {
    padding: 1.5rem;
  }

  .modal-title {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 700;
    color: #1b1a1a;
  }

  .modal-description {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
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

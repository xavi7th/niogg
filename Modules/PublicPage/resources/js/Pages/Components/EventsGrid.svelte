<script>
  import { Link, page } from '@inertiajs/svelte';
  import { modalRoot } from "@/stores";
  import { Portal } from "svelte-teleport";
  import LazyThumbnail from "./LazyThumbnail.svelte";

  export let events = [];
  export let onBackToTimeline = () => {};

  let pageModals = null;
  let modalVideo = null;
  let selectedCategory = 'all';
  let modalVideoElement = null;
  let playAttempted = false;
  let videoError = null;
  let retryCount = 0;
  const MAX_RETRIES = 3;

  const categories = Array.from(
    new Set(events.map((e) => e.category || 'uncategorized'))
  ).filter(c => c !== 'all');

  const getFilteredVideos = () => {
    if (selectedCategory === 'all') {
      return events.flatMap((e) =>
        (e.videos || []).map((v) => ({
          ...v,
          eventCategory: e.category || 'Uncategorized'
        }))
      );
    }
    return events
      .filter((e) => (e.category || 'uncategorized') === selectedCategory)
      .flatMap((e) =>
        (e.videos || []).map((v) => ({
          ...v,
          eventCategory: e.category || 'Uncategorized'
        }))
      );
  };

  const getCategoryLabel = (category) => {
    if (!category) return '';
    if (category === 'uncategorized') return 'Uncategorized';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

  const openVideoModal = (video) => {
    modalVideo = video;
    playAttempted = false;
    videoError = null;
    retryCount = 0;

    setTimeout(() => {
      pageModals.teleport_to($modalRoot);
    }, 300);
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

  const handleVideoError = (event) => {
    const video = event.target;
    const error = video.error;

    if (retryCount < MAX_RETRIES) {
      console.error('Video load error:', error);
      videoError = {
        code: error?.code || 'UNKNOWN',
        message: getVideoErrorMessage(error?.code),
        retryable: retryCount < MAX_RETRIES
      };
    }
  };

  const getVideoErrorMessage = (code) => {
    switch (code) {
      case 1: return 'Video fetching aborted';
      case 2: return 'Network error during video load';
      case 3: return 'Video decoding error';
      case 4: return 'Video format or source not supported';
      default: return 'Unable to load video';
    }
  };

  const handleRetry = () => {
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = null;
      if (modalVideoElement) {
        modalVideoElement.load();
        // handleCanPlay will trigger play() on next canplay event
      }
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

  $: filteredVideos = getFilteredVideos();

  $: currentSort = $page.url.searchParams.get('sort') || 'newest';


</script>

<div class="events-grid-view">
  <div class="grid-header">
    <button on:click={onBackToTimeline} class="btn-back">← Back to Timeline</button>
    <div class="sort-controls">
      <span class="sort-label">Sort events:</span>
      <Link href="?sort=newest" class="sort-link {currentSort === 'newest' ? 'active' : ''}" aria-current={currentSort === 'newest' ? 'true' : undefined}>
        Newest
      </Link>
      <span class="sort-divider">/</span>
      <Link href="?sort=oldest" class="sort-link {currentSort === 'oldest' ? 'active' : ''}" aria-current={currentSort === 'oldest' ? 'true' : undefined}>
        Oldest
      </Link>
    </div>
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
        </div>

        <div class="card-content">
          <h4 class="card-title">{video.title}</h4>
          <div class="card-info">
            {#if video.format_duration}
              <span class="duration">{video.format_duration}</span>
            {/if}
            {#if video.eventCategory}
              <span class="category">{getCategoryLabel(video.eventCategory)}</span>
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

<!-- Video Modal -->
{#if modalVideo}
  <Portal bind:this={pageModals}>
    <div class="video-modal" on:click={closeVideoModal} on:keydown={(e) => e.key === 'Escape' && closeVideoModal()} role="dialog" aria-modal="true">
      <div class="modal-content" on:click|stopPropagation>
        <button class="modal-close" on:click={closeVideoModal} aria-label="Close modal">×</button>
        <div class="modal-video-wrapper">
          {#if videoError}
            <div class="video-error-overlay">
              <p class="error-message">{videoError.message}</p>
              {#if videoError.retryable && retryCount < MAX_RETRIES}
                <button on:click={handleRetry} class="btn-retry">Retry</button>
              {/if}
            </div>
          {/if}
          <video
            bind:this={modalVideoElement}
            src={modalVideo.video_url}
            poster={modalVideo.thumbnail_url}
            controls
            autoplay
            muted
            playsinline
            on:canplay={handleCanPlay}
            on:error={handleVideoError}
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
  </Portal>
{/if}

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
    font-size: clamp(0.875rem, 2vw, 1rem);
    cursor: pointer;
    padding: 0.5rem 1rem;
    transition: color 0.2s ease;
    min-height: 48px;
    min-width: 48px;
    display: flex;
    align-items: center;
  }

  .btn-back:hover {
    color: #e66d06;
  }

  .sort-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1rem;
  }

  .sort-label {
    color: #666;
    font-size: 0.875rem;
    font-weight: 500;
  }

  .sort-controls a {
    color: #666;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
  }

  .sort-controls a:hover {
    color: #ff7607;
  }

  .sort-controls a.active {
    color: #ff7607;
    text-decoration: underline;
  }

  .sort-divider {
    color: #ccc;
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
    font-size: clamp(0.875rem, 2vw, 1rem);
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    min-height: 48px;
    display: flex;
    align-items: center;
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

  .video-error-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    z-index: 5;
    padding: 2rem;
    text-align: center;
  }

  .error-message {
    color: white;
    font-size: 1rem;
    margin: 0;
  }

  .btn-retry {
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .btn-retry:hover {
    background-color: #e66d06;
  }

  @media (max-width: 991px) {
    .video-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .video-card {
      will-change: transform;
      transform: translateZ(0);
    }
  }

  @media (max-width: 768px) {
    .video-grid {
      grid-template-columns: 1fr;
    }

    .events-grid-view {
      padding: 1rem 0.75rem;
    }

    .video-card {
      will-change: transform;
      transform: translateZ(0);
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .video-card:active {
        transform: translateY(-2px) scale(0.98);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      }

      .video-card {
        -webkit-tap-highlight-color: transparent;
      }

      .play-button {
        -webkit-tap-highlight-color: transparent;
      }
    }

    /* Enhanced touch targets */
    .play-button {
      min-height: 60px;
      min-width: 60px;
      width: 60px !important;
      height: 60px !important;
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .video-card:hover {
        transform: none !important;
      }

      .video-card:active {
        transform: none !important;
      }

      .play-button {
        transition: none !important;
      }
    }
  }
</style>

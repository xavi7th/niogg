<script>
  import { Link, page } from '@inertiajs/svelte';
  import EventHeader from '@publicpage-pages/Components/EventHeader.svelte';
  import VideoPlayer from '@publicpage-pages/Components/VideoPlayer.svelte';
  import SupportingVideoGrid from '@publicpage-pages/Components/SupportingVideoGrid.svelte';

  export let events = [];
  export let onViewToggle = () => {};

  let selectedVideos = new Map();

  $: events.forEach((event) => {
    if (!selectedVideos.has(event.id)) {
      const featured = event.videos?.find((v) => v.is_featured);
      selectedVideos.set(event.id, featured?.id);
    }
  });

  const handleVideoSelect = (eventId, videoId) => {
    selectedVideos.set(eventId, videoId);
    selectedVideos = selectedVideos;

    const playerElement = document.querySelector(`[data-event-player="${eventId}"]`);
    if (playerElement) {
      playerElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  const getSelectedVideo = (eventId) => {
    const videoId = selectedVideos.get(eventId);
    return events.find((e) => e.id === eventId)?.videos?.find((v) => v.id === videoId);
  };

  $: currentSort = $page.url?.searchParams.get('sort') || 'newest';
</script>

<div class="event-timeline">
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
  {#each events as event, idx (event.id)}
    <section class="event-section" style={`background-color: ${idx % 2 === 0 ? '#ffffff' : '#f9f9f9'}`}>
      <div class="event-container">
        <EventHeader {event} />

        <div data-event-player={event.id} class="featured-player-section">
          {#if getSelectedVideo(event.id)}
            {#key getSelectedVideo(event.id)?.id || 'empty'}
              <VideoPlayer video={getSelectedVideo(event.id)} size="large" />
            {/key}
          {/if}
        </div>

        {#if event.videos && event.videos.length > 1}
          <div class="supporting-videos-section">
            <SupportingVideoGrid
              videos={event.videos.filter((v) => !v.is_featured)}
              currentlyPlaying={selectedVideos.get(event.id)}
              onVideoSelect={(video) => handleVideoSelect(event.id, video.id)}
            />
          </div>
        {/if}
      </div>
    </section>
  {/each}
</div>

<style>
  .event-timeline {
    width: 100%;
    display: block;
  }

  .event-section {
    padding: 3rem 0;
    transition: background-color 0.2s ease;
  }

  .event-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
  }

  .sort-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    padding: 0 1rem;
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

  .featured-player-section {
    margin: 2rem 0;
  }

  .supporting-videos-section {
    margin: 0 0 2rem 0;
  }

  .btn-primary {
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

  .btn-primary:hover {
    background-color: #e66d06;
  }

  .btn-primary {
    will-change: transform, background-color;
    transform: translateZ(0);
  }

  @media (max-width: 768px) {
    .event-timeline {
      display: none;
      will-change: transform;
    }

    .event-section {
      padding: 1.5rem 0;
    }

    .event-container {
      padding: 0 0.75rem;
      max-width: 100%;
      will-change: transform;
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .btn-primary:active {
        transform: scale(0.95);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      }
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .btn-primary {
        transition: none !important;
      }
    }
  }

  /* Tablet optimizations */
  @media (max-width: 991px) and (min-width: 769px) {
    .event-container {
      will-change: transform;
    }
  }
</style>

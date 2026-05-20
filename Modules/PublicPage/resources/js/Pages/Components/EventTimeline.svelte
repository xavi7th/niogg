<script>
  import { Link, page } from '@inertiajs/svelte';
  import EventHeader from '@publicpage-pages/Components/EventHeader.svelte';
  import VideoPlayer from '@publicpage-pages/Components/VideoPlayer.svelte';

  export let events = [];
  export let onViewToggle = () => {};

  const getFeaturedVideo = (event) => {
    return event.videos?.find((v) => v.is_featured) ?? event.videos?.[0] ?? null;
  };

  $: currentSort = $page.url?.searchParams?.get('sort') || 'newest';
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

        <div class="featured-player-section">
          {#if getFeaturedVideo(event)}
            {#key getFeaturedVideo(event)?.id}
              <VideoPlayer video={getFeaturedVideo(event)} size="large" />
            {/key}
          {/if}
        </div>

        <div class="view-event-link">
          <a href="/events/{event.slug}" class="btn-view-event">
            View Event Details
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </a>
        </div>
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

  .view-event-link {
    margin-top: 1.5rem;
  }

  .btn-view-event {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    font-size: clamp(0.875rem, 2vw, 1rem);
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.2s ease;
    min-height: 48px;
  }

  .btn-view-event:hover {
    background-color: #e66d06;
  }

  .btn-view-event:active {
    background-color: #d55b04;
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

<script>
  import { router } from '@inertiajs/svelte';
  import EventHeader from '@publicpage-pages/Components/EventHeader.svelte';
  import VideoPlayer from '@publicpage-pages/Components/VideoPlayer.svelte';

  export let events = [];
  export let onViewToggle = () => {};

  const navigateToEventGrid = (eventSlug) => {
    router.visit(`/events/${eventSlug}/videos`);
  };
</script>

<div class="events-featured-only">
  {#each events as event, idx (event.id)}
    <section class="event-section" style={`background-color: ${idx % 2 === 0 ? '#ffffff' : '#f9f9f9'}`}>
      <div class="event-container">
        <EventHeader {event} />

        <div class="featured-player-section">
          {#if event.videos && event.videos.length > 0}
            {#if event.videos.find((v) => v.is_featured)}
              <VideoPlayer video={event.videos.find((v) => v.is_featured)} size="large" />
            {/if}
          {/if}
        </div>

        <div class="see-more-button">
          <button
            on:click={() => navigateToEventGrid(event.slug)}
            class="btn-see-more"
          >
            See more from {event.name}
          </button>
        </div>
      </div>
    </section>
  {/each}

  <div class="view-all-button">
    <button on:click={onViewToggle} class="btn-primary">View All Videos</button>
  </div>
</div>

<style>
  .events-featured-only {
    width: 100%;
    display: none;
  }

  .event-section {
    padding: 1.5rem 0;
    transition: background-color 0.2s ease;
  }

  .event-container {
    max-width: 100%;
    padding: 0 0.75rem;
  }

  .featured-player-section {
    margin: 1.5rem 0;
  }

  .see-more-button {
    margin: 1.5rem 0;
  }

  .btn-see-more {
    width: 100%;
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 0.25rem;
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    cursor: pointer;
    transition: background-color 0.2s ease;
    min-height: 48px;
    min-width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-see-more:hover {
    background-color: #e66d06;
  }

  .btn-see-more:active {
    background-color: #d55b04;
  }

  .view-all-button {
    text-align: center;
    padding: 1.5rem 0.75rem;
  }

  .btn-primary {
    width: 100%;
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 0.25rem;
    font-size: clamp(0.75rem, 2vw, 0.875rem);
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

  .btn-primary:active {
    background-color: #d55b04;
  }

  .btn-primary {
    will-change: transform, background-color;
    transform: translateZ(0);
  }

  @media (max-width: 768px) {
    .events-featured-only {
      display: block;
      will-change: transform;
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
      .btn-primary:active {
        transform: scale(0.95);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      }

      .btn-primary {
        -webkit-tap-highlight-color: transparent;
        user-select: none;
      }
    }

    /* Enhanced touch targets for mobile */
    .btn-primary {
      min-height: 56px;
      min-width: 56px;
    }

    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      .btn-primary {
        transition: none !important;
      }
    }
  }
</style>

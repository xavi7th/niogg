<script>
  import { modalRoot } from "@/stores";
  import { Portal } from "svelte-teleport";
  import { router } from '@inertiajs/svelte';
  import EventHeader from '@publicpage-pages/Components/EventHeader.svelte';

  export let events = [];
  export let onViewToggle = () => {};

  let modalVideo = null;
  let pageModals = undefined;
  let modalVideoElement = null;

  const navigateToEventGrid = (eventSlug) => {
    router.visit(`/events/${eventSlug}/videos`);
  };

  const openVideoModal = (video) => {
    modalVideo = video;

    setTimeout(() => {
      pageModals.teleport_to($modalRoot);
    }, 300);
  };

  const closeVideoModal = () => {
    if (modalVideoElement) {
      modalVideoElement.pause();
    }
    modalVideo = null;
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
              {@const featuredVideo = event.videos.find((v) => v.is_featured)}
              <div class="featured-video-card" on:click={() => openVideoModal(featuredVideo)} role="button" tabindex="0">
                <div class="card-image">
                  <img src={featuredVideo.thumbnail_url} alt={featuredVideo.title} loading="lazy" />
                  <div class="play-button">
                    <svg width="50" height="50" viewBox="0 0 100 100" fill="currentColor">
                      <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="2" />
                      <polygon points="35,20 35,80 80,50" fill="currentColor" />
                    </svg>
                  </div>
                  <span class="featured-badge">Featured</span>
                </div>
                <div class="card-content">
                  <h4 class="card-title">{featuredVideo.title}</h4>
                  {#if featuredVideo.format_duration}
                    <div class="card-info">
                      <span class="duration">{featuredVideo.format_duration}</span>
                    </div>
                  {/if}
                </div>
              </div>
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
</div>

<!-- Video Modal -->
{#if modalVideo}
<Portal bind:this={pageModals}>
  <div class="video-modal" on:click={closeVideoModal} on:keydown={(e) => e.key === 'Escape' && closeVideoModal()} role="dialog" aria-modal="true">
    <div class="modal-content" on:click|stopPropagation>
      <button class="modal-close" on:click={closeVideoModal} aria-label="Close modal">×</button>
      <div class="modal-video-wrapper">
        <video
          bind:this={modalVideoElement}
          src={modalVideo.video_url}
          poster={modalVideo.thumbnail_url}
          controls
          autoplay
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

  @media (max-width: 768px) {
    .events-featured-only {
      display: block;
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

  /* Featured Video Card */
  .featured-video-card {
    border-radius: 4px;
    overflow: hidden;
    transition: transform 0.3s ease;
    cursor: pointer;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .featured-video-card:active {
    transform: scale(0.98);
  }

  .featured-video-card:focus-visible {
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
    z-index: 2;
  }

  .card-content {
    padding: 1rem;
  }

  .card-title {
    margin: 0 0 0.5rem 0;
    font-size: clamp(0.875rem, 2.5vw, 1rem);
    font-weight: 700;
    color: #1b1a1a;
  }

  .card-info {
    font-size: clamp(0.75rem, 2vw, 0.875rem);
    color: #9b9b9b;
  }

  /* Video Modal */
  .video-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.9);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    overflow-y: auto;
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
</style>

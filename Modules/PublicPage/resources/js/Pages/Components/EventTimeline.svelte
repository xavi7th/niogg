<script>
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
</script>

<div class="event-timeline">
  {#each events as event, idx (event.id)}
    <section class="event-section" style={`background-color: ${idx % 2 === 0 ? '#ffffff' : '#f9f9f9'}`}>
      <div class="event-container">
        <EventHeader {event} />

        <div data-event-player={event.id} class="featured-player-section">
          {#if getSelectedVideo(event.id)}
            <VideoPlayer video={getSelectedVideo(event.id)} size="large" />
          {/if}
        </div>

        {#if event.videos && event.videos.length > 1}
          <SupportingVideoGrid
            videos={event.videos.filter((v) => !v.is_featured)}
            currentlyPlaying={selectedVideos.get(event.id)}
            onVideoSelect={(video) => handleVideoSelect(event.id, video.id)}
          />
        {/if}
      </div>
    </section>
  {/each}

  <div class="view-all-button">
    <button on:click={onViewToggle} class="btn-primary">View All Videos</button>
  </div>
</div>

<style>
  .event-timeline {
    width: 100%;
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

  .featured-player-section {
    margin: 2rem 0;
  }

  .view-all-button {
    text-align: center;
    padding: 2rem 1rem;
  }

  .btn-primary {
    background-color: #ff7607;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .btn-primary:hover {
    background-color: #e66d06;
  }

  @media (max-width: 768px) {
    .event-section {
      padding: 1.5rem 0;
    }

    .event-container {
      padding: 0 0.75rem;
    }
  }
</style>

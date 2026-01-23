<script>
  import { page } from '@inertiajs/svelte';
  import PublicPageLayout from '@publicpage-pages/Layouts/PublicPageLayout.svelte';
  import EventTimeline from '@publicpage-pages/Components/EventTimeline.svelte';
  import EventsGrid from '@publicpage-pages/Components/EventsGrid.svelte';

  export let events = [];
  export let pageTitle = 'Events Media Showcase';

  let viewMode = 'timeline';

  const handleToggleToGrid = () => {
    viewMode = 'grid';
  };

  const handleBackToTimeline = () => {
    viewMode = 'timeline';
  };
</script>

<PublicPageLayout {pageTitle}>
  <div class="events-media-showcase">
    {#if viewMode === 'timeline'}
      <EventTimeline {events} onViewToggle={handleToggleToGrid} />
    {:else}
      <EventsGrid {events} onBackToTimeline={handleBackToTimeline} />
    {/if}
  </div>
</PublicPageLayout>

<style>
  .events-media-showcase {
    width: 100%;
  }
</style>

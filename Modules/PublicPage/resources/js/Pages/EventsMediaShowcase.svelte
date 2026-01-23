<script>
  import { page } from '@inertiajs/svelte';
  import PublicPageLayout from '@publicpage-pages/Layouts/PublicPageLayout.svelte';
  import EventTimeline from '@publicpage-pages/Components/EventTimeline.svelte';
  import EventsGrid from '@publicpage-pages/Components/EventsGrid.svelte';
  import EventsFeaturedOnly from '@publicpage-pages/Components/EventsFeaturedOnly.svelte';

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
      <EventsFeaturedOnly {events} onViewToggle={handleToggleToGrid} />
    {:else}
      <EventsGrid {events} onBackToTimeline={handleBackToTimeline} />
    {/if}
  </div>
</PublicPageLayout>

<style>
  .events-media-showcase {
    width: 100%;
    will-change: transform;
    transform: translateZ(0);
    contain: layout style paint;
  }

  /* Performance optimizations */
  .events-media-showcase * {
    box-sizing: border-box;
  }

  /* Improve rendering performance */
  .events-media-showcase {
    content-visibility: auto;
    contain-intrinsic-size: 2000px;
  }

  /* Enhanced mobile touch targets */
  @media (max-width: 768px) {
    .events-media-showcase {
      touch-action: pan-y pinch-zoom;
    }
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    * {
      transition-duration: 0.01ms !important;
      animation-duration: 0.01ms !important;
      transition-property: none !important;
    }
  }

  /* Touch device specific styles */
  @media (hover: none) and (pointer: coarse) {
    .btn-primary:active {
      transform: scale(0.98);
    }

    /* Remove tap highlight on touch devices */
    .btn-primary {
      -webkit-tap-highlight-color: transparent;
      -webkit-touch-callout: none;
      user-select: none;
    }
  }
</style>

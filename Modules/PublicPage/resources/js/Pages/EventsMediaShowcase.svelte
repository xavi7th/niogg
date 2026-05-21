<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from '@inertiajs/svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import MediaShowcaseHero from '@publicpage-pages/Components/MediaShowcaseHero.svelte';
  import EventsGrid from '@publicpage-pages/Components/EventsGrid.svelte';
  import EventTimeline from '@publicpage-pages/Components/EventTimeline.svelte';
  import EventsFeaturedOnly from '@publicpage-pages/Components/EventsFeaturedOnly.svelte';

  export let events = [];

  $: ({ app } = $page.props);

  let viewMode = 'timeline';

  const handleToggleToGrid = () => {
    viewMode = 'grid';
  };

  const handleBackToTimeline = () => {
    viewMode = 'timeline';
  };
</script>

<PageTitle appName={app.name} pageTitle='Events Media Showcase'>
  <li class="breadcrumb-item active" aria-current="page">Events Media Showcase</li>
</PageTitle>

<MediaShowcaseHero onViewToggle={handleToggleToGrid} />

<div class="events-media-showcase">
  {#if viewMode === 'timeline'}
    <EventTimeline {events} onViewToggle={handleToggleToGrid} />
    <EventsFeaturedOnly {events} onViewToggle={handleToggleToGrid} />
  {:else}
    <EventsGrid {events} onBackToTimeline={handleBackToTimeline} />
  {/if}
</div>

<style>
  .events-media-showcase {
    width: 100%;
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

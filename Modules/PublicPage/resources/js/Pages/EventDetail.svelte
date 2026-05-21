<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import EventHeader from '@publicpage-pages/Components/EventHeader.svelte';
  import EventPhotoGallery from '@publicpage-components/EventPhotoGallery.svelte';
  import EventVideoPlaylist from '@publicpage-components/EventVideoPlaylist.svelte';

  $: ({ event, app } = $page.props);

  $: sortedPhotos = event?.photos
    ? [...event.photos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];

  $: sortedVideos = event?.videos
    ? [...event.videos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];
</script>

<PageTitle appName={app.name} pageTitle={event?.name || 'Event Detail'}>
  <li class="breadcrumb-item"><a href={route('events.media-showcase')}>Events Media Showcase</a></li>
  <li class="breadcrumb-item active" aria-current="page">{event?.name}</li>
</PageTitle>

<EventHeader {event} />

<section class="py-12 bg-gray-50">
  <div class="container">
    <!-- {#if event?.description}
      <div class="mb-10 max-w-3xl">
        <p class="text-lg text-[#555] leading-relaxed">{event.description}</p>
      </div>
    {/if} -->

    {#if sortedPhotos.length > 0}
      <div class="mb-12">
        <EventPhotoGallery photos={sortedPhotos} />
      </div>
    {/if}

    {#if sortedVideos.length > 0}
      <div class="mb-12">
        <EventVideoPlaylist videos={sortedVideos} />
      </div>
    {/if}

    {#if sortedPhotos.length === 0 && sortedVideos.length === 0}
      <div class="text-center py-16">
        <p class="text-[#9b9b9b]">No media available for this event yet.</p>
        <a href={route('events.media-showcase')} class="text-[#ff7607] hover:underline mt-2 inline-block">
          Back to events
        </a>
      </div>
    {/if}
  </div>
</section>

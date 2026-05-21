<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import { router } from '@inertiajs/svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app, photos, categories, activeCategory } = $page.props);

  let photoList = [];
  let lightboxPhotoIndex = null;
  let isAppending = false;

  // Replace photoList on category change; append on Load More
  $: if (photos?.data && !isAppending) {
    photoList = photos.data;
  }

  function filterByCategory(category) {
    isAppending = false;
    router.get(
      route('app.gallery', category ? { category } : {}),
      {},
      {
        preserveState: true,
        preserveScroll: false,
        only: ['photos', 'activeCategory'],
        onSuccess: () => {
          const mixer = window.$?.('#filtered-items-wrap')?.data('mixItUp');
          if (mixer) {
            setTimeout(() => {
              mixer.destroy();
              window.$?.('#filtered-items-wrap').mixItUp();
            }, 600);
          }
        },
      }
    );
  }

  function loadMore() {
    if (!photos?.next_page_url) return;
    isAppending = true;
    router.visit(photos.next_page_url, {
      method: 'get',
      preserveState: true,
      preserveScroll: true,
      only: ['photos'],
      onSuccess: () => {
        photoList = [...photoList, ...(photos.data ?? [])];
        isAppending = false;
      },
      onError: () => {
        isAppending = false;
      },
    });
  }

  function openLightbox(index) {
    lightboxPhotoIndex = index;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightboxPhotoIndex = null;
    document.body.style.overflow = '';
  }

  function prevPhoto() {
    if (lightboxPhotoIndex > 0) lightboxPhotoIndex--;
  }

  function nextPhoto() {
    if (lightboxPhotoIndex < photoList.length - 1) lightboxPhotoIndex++;
  }

  function handleKeydown(e) {
    if (lightboxPhotoIndex === null) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<PageTitle appName={app.name} pageTitle="Gallery">
  <li class="breadcrumb-item active" aria-current="page">Gallery</li>
</PageTitle>

<section id="projectsGrid" class="projects projects-grid">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <ul class="projects-filter justify-content-center">
          <li>
            <a
              class="filter {!activeCategory ? 'active' : ''}"
              href="#"
              on:click|preventDefault={() => filterByCategory(null)}
            >All</a>
          </li>
          {#each categories as category}
            <li>
              <a
                class="filter {activeCategory === category ? 'active' : ''}"
                href="#"
                on:click|preventDefault={() => filterByCategory(category)}
              >{category}</a>
            </li>
          {/each}
        </ul>
      </div>
    </div>

    <div id="filtered-items-wrap" class="row">
      {#each photoList as photo, i}
        <div class="col-sm-6 col-md-6 col-lg-4 mix">
          <div class="project-item">
            <div class="project__img">
              <img
                src={photo.thumbnail_url}
                alt={photo.alt_text || ''}
                class="img-fluid w-full cursor-pointer"
                loading="lazy"
                on:click={() => openLightbox(i)}
              />
              <div class="service__overlay" style="pointer-events: none;">
                <a href="#" on:click|preventDefault={() => openLightbox(i)} class="zoom__icon" style="pointer-events: auto;" on:click|stopPropagation>
                  <i class="icon-link"></i>
                </a>
              </div>
            </div>
            <div class="project__content">
              <h4 class="project__title">
                <a href={route('events.show', { event: photo.event?.slug })}>
                  {photo.event?.name || 'Event'}
                </a>
              </h4>
              <div class="project__cat">
                <a href="#">{photo.event?.category || 'Uncategorized'}</a>
              </div>
            </div>
          </div>
        </div>
      {/each}

      {#if photoList.length === 0}
        <div class="col-12 text-center py-16">
          <p class="text-[#9b9b9b]">No photos found.</p>
        </div>
      {/if}
    </div>

    {#if photos?.next_page_url}
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 text-center">
          <button on:click={loadMore} type="button" class="btn btn__primary btn__hover3 mt-20 loadMoreProjects">
            Load More
          </button>
        </div>
      </div>
    {/if}
  </div>
</section>

{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
    aria-modal="true"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photoList.length}
      </p>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <img
        src={photoList[lightboxPhotoIndex].photo_url}
        alt={photoList[lightboxPhotoIndex].alt_text || ''}
        class="max-h-[80vh] mx-auto object-contain rounded-lg"
      />

      {#if photoList[lightboxPhotoIndex].alt_text}
        <p class="text-white text-center mt-4 text-sm opacity-80">{photoList[lightboxPhotoIndex].alt_text}</p>
      {/if}

      <p class="text-center mt-2">
        <a
          href={route('events.show', { event: photoList[lightboxPhotoIndex].event?.slug })}
          class="text-[#ff7607] hover:underline text-sm"
        >View event →</a>
      </p>

      {#if lightboxPhotoIndex > 0}
        <button on:click={prevPhoto} class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      {/if}
      {#if lightboxPhotoIndex < photoList.length - 1}
        <button on:click={nextPhoto} class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      {/if}
    </div>
  </div>
{/if}

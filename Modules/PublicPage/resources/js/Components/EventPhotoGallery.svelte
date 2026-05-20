<script>
  export let photos = [];

  const PREVIEW_CAP = 12;

  let lightboxPhotoIndex = null;

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
    if (lightboxPhotoIndex < photos.length - 1) lightboxPhotoIndex++;
  }

  function handleKeydown(e) {
    if (lightboxPhotoIndex === null) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevPhoto();
    if (e.key === 'ArrowRight') nextPhoto();
  }
</script>

<svelte:window on:keydown={handleKeydown} />

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Photo Gallery</h2>

<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 sm:gap-3">
  {#each photos.slice(0, PREVIEW_CAP) as photo, i}
    <button
      on:click={() => openLightbox(i)}
      class="aspect-square rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:ring-2 hover:ring-[#ff7607] transition-all cursor-pointer bg-gray-100"
      type="button"
    >
      <img
        src={photo.thumbnail_url}
        alt={photo.alt_text || ''}
        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />
    </button>
  {/each}
</div>

{#if photos.length > PREVIEW_CAP}
  <div class="text-center mt-6">
    <button
      on:click={() => openLightbox(0)}
      class="px-6 py-3 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium transition-colors"
      type="button"
    >
      View all {photos.length} photos
    </button>
  </div>
{/if}

{#if lightboxPhotoIndex !== null}
  <div
    class="fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center"
    on:click={closeLightbox}
    role="dialog"
    aria-modal="true"
  >
    <div class="relative max-w-5xl w-full mx-4" on:click|stopPropagation>
      <p class="absolute -top-10 left-0 text-white text-sm opacity-70">
        {lightboxPhotoIndex + 1} / {photos.length}
      </p>
      <button on:click={closeLightbox} class="absolute -top-12 right-0 text-white hover:text-[#ff7607]" type="button">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <img
        src={photos[lightboxPhotoIndex].photo_url}
        alt={photos[lightboxPhotoIndex].alt_text || ''}
        class="max-h-[80vh] mx-auto object-contain rounded-lg"
      />

      {#if photos[lightboxPhotoIndex].alt_text}
        <p class="text-white text-center mt-4 text-sm opacity-80">{photos[lightboxPhotoIndex].alt_text}</p>
      {/if}

      {#if lightboxPhotoIndex > 0}
        <button on:click={prevPhoto} class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
      {/if}
      {#if lightboxPhotoIndex < photos.length - 1}
        <button on:click={nextPhoto} class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-[#ff7607]" type="button">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      {/if}
    </div>
  </div>
{/if}

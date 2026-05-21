<script>
  import { router } from '@inertiajs/svelte';

  export let event = null;
  export let showPhotoUpload = false;

  let photoDragId = null;
  let photoDragOverId = null;
  let isPhotoReordering = false;
  let photoFileInput;
  let newPhotoFiles = [];
  let newPhotoPreviews = [];
  let isUploading = false;
  let showPhotoDeleteDialog = false;
  let photoToDelete = null;

  $: sortedPhotos = event?.photos
    ? [...event.photos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    : [];

  function handlePhotoDragStart(photoId, e) {
    photoDragId = photoId;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', photoId.toString());
    e.target.closest('.group').style.opacity = '0.5';
  }

  function handlePhotoDragEnd(e) {
    photoDragId = null;
    photoDragOverId = null;
    if (e.target.closest) e.target.closest('.group').style.opacity = '1';
  }

  function handlePhotoDragOver(photoId, e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    if (photoDragId !== photoId) photoDragOverId = photoId;
  }

  function handlePhotoDragLeave(photoId) {
    if (photoDragOverId === photoId) photoDragOverId = null;
  }

  async function handlePhotoDrop(photoId, e) {
    e.preventDefault();
    photoDragOverId = null;
    if (photoDragId === null || photoDragId === photoId) return;

    const draggedIndex = sortedPhotos.findIndex((p) => p.id === photoDragId);
    const dropIndex = sortedPhotos.findIndex((p) => p.id === photoId);
    if (draggedIndex === -1 || dropIndex === -1) return;

    const newOrder = [...sortedPhotos];
    const [removed] = newOrder.splice(draggedIndex, 1);
    newOrder.splice(dropIndex, 0, removed);

    isPhotoReordering = true;
    try {
      const response = await fetch(`/admin/events/${event.id}/photos/reorder`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ photo_ids: newOrder.map((p) => p.id) }),
      });
      if (response.ok) router.reload({ only: ['event'] });
    } catch (err) {
      console.error('Reorder failed', err);
    } finally {
      isPhotoReordering = false;
      photoDragId = null;
    }
  }

  function handlePhotoSelect(e) {
    addPhotoFiles(e.target.files);
    e.target.value = '';
  }

  function handlePhotoDropEvent(e) {
    addPhotoFiles(e.dataTransfer.files);
  }

  function addPhotoFiles(files) {
    const remaining = 20 - (event?.photos?.length || 0) - newPhotoFiles.length;
    Array.from(files)
      .slice(0, remaining)
      .forEach((file) => {
        newPhotoFiles = [...newPhotoFiles, file];
        newPhotoPreviews = [...newPhotoPreviews, URL.createObjectURL(file)];
      });
  }

  function removeNewPhoto(index) {
    URL.revokeObjectURL(newPhotoPreviews[index]);
    newPhotoFiles = newPhotoFiles.filter((_, i) => i !== index);
    newPhotoPreviews = newPhotoPreviews.filter((_, i) => i !== index);
  }

  async function uploadPhotos() {
    if (newPhotoFiles.length === 0) return;
    isUploading = true;

    const form = new FormData();
    newPhotoFiles.forEach((file, i) => form.append(`photos[${i}]`, file));

    try {
      const response = await fetch(`/admin/events/${event.id}/photos`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: form,
      });

      if (response.ok) {
        closePhotoUpload();
        router.reload({ only: ['event'] });
        window.ToastLarge.fire({ title: 'Success', html: 'Photos uploaded.', icon: 'success', timer: 3000 });
      } else {
        const data = await response.json();
        window.ToastLarge.fire({ title: 'Error', html: data.message || 'Upload failed.', icon: 'error', timer: 5000 });
      }
    } catch (err) {
      window.ToastLarge.fire({ title: 'Error', html: 'Network error.', icon: 'error', timer: 5000 });
    } finally {
      isUploading = false;
    }
  }

  function closePhotoUpload() {
    showPhotoUpload = false;
    newPhotoFiles.forEach((_, i) => URL.revokeObjectURL(newPhotoPreviews[i]));
    newPhotoFiles = [];
    newPhotoPreviews = [];
  }

  async function retryThumbnail(photoId) {
    try {
      const response = await fetch(`/admin/photos/${photoId}/retry-thumbnail`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      if (response.ok) {
        window.ToastLarge.fire({ title: 'Queued', html: 'Thumbnail generation queued.', icon: 'info', timer: 3000 });
      }
    } catch (err) {
      console.error('Retry thumbnail failed', err);
    }
  }

  async function updatePhotoAltText(photoId, altText) {
    try {
      await fetch(`/admin/photos/${photoId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ alt_text: altText }),
      });
    } catch (err) {
      console.error('Alt text update failed', err);
    }
  }

  function openPhotoDeleteDialog(photo) {
    photoToDelete = photo;
    showPhotoDeleteDialog = true;
  }

  function closePhotoDeleteDialog() {
    showPhotoDeleteDialog = false;
    photoToDelete = null;
  }

  async function confirmDeletePhoto() {
    if (!photoToDelete) return;
    try {
      const response = await fetch(`/admin/photos/${photoToDelete.id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      if (response.ok) {
        showPhotoDeleteDialog = false;
        photoToDelete = null;
        router.reload({ only: ['event'] });
        window.ToastLarge.fire({ title: 'Success', html: 'Photo deleted.', icon: 'success', timer: 3000 });
      }
    } catch (err) {
      window.ToastLarge.fire({ title: 'Error', html: 'Network error.', icon: 'error', timer: 5000 });
    }
  }
</script>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
  <div>
    <h2 class="text-lg font-semibold text-[#1b1a1a]">Photos ({sortedPhotos.length})</h2>
    {#if sortedPhotos.length > 1}
      <p class="text-sm text-[#9b9b9b] mt-1">Drag photos to reorder them</p>
    {/if}
  </div>
</div>

{#if sortedPhotos.length > 0}
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
    {#each sortedPhotos as photo}
      <div
        draggable="true"
        on:dragstart={(e) => handlePhotoDragStart(photo.id, e)}
        on:dragend={handlePhotoDragEnd}
        on:dragover={(e) => handlePhotoDragOver(photo.id, e)}
        on:dragleave={() => handlePhotoDragLeave(photo.id)}
        on:drop={(e) => handlePhotoDrop(photo.id, e)}
        class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 border border-[#eaeaea] cursor-grab active:cursor-grabbing"
        class:ring-2={photoDragOverId === photo.id}
        class:ring-[#ff7607]={photoDragOverId === photo.id}
      >
        {#if photo.thumbnail_url}
          <img src={photo.thumbnail_url} alt={photo.alt_text || ''} class="w-full h-full object-cover" loading="lazy" />
        {:else}
          <div class="w-full h-full flex flex-col items-center justify-center gap-1 p-2">
            <svg class="w-8 h-8 text-[#9b9b9b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <button
              on:click|stopPropagation={() => retryThumbnail(photo.id)}
              type="button"
              class="text-xs text-[#ff7607] underline"
            >Retry</button>
          </div>
        {/if}

        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
          <button
            on:click={() => openPhotoDeleteDialog(photo)}
            type="button"
            class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 shadow"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent p-2 pt-6 opacity-0 group-hover:opacity-100 transition-opacity">
          <input
            type="text"
            value={photo.alt_text || ''}
            placeholder="Add alt text..."
            on:blur={(e) => updatePhotoAltText(photo.id, e.target.value)}
            class="w-full px-2 py-1 text-xs text-white bg-white/20 backdrop-blur-sm rounded border border-white/30 placeholder-white/50 focus:outline-none focus:ring-1 focus:ring-white"
          />
        </div>
      </div>
    {/each}
  </div>
{:else}
  <div class="bg-white rounded-lg border border-[#eaeaea] p-12 text-center">
    <svg class="w-16 h-16 text-[#9b9b9b] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    <h3 class="text-lg font-medium text-[#1b1a1a] mb-2">No photos yet</h3>
    <p class="text-[#9b9b9b] mb-4">Add photos to this event to make them appear in the public gallery.</p>
    <button
      on:click={() => (showPhotoUpload = true)}
      class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
      type="button"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
      Add First Photo
    </button>
  </div>
{/if}

{#if showPhotoUpload}
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <!-- svelte-ignore a11y-click-events-have-key-events -->
    <!-- svelte-ignore a11y-no-static-element-interactions -->
    <div class="bg-white rounded-xl max-w-lg w-full p-6" on:click|stopPropagation>
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-[#1b1a1a]">Upload Photos</h3>
        <button on:click={closePhotoUpload} type="button" class="text-[#9b9b9b] hover:text-[#1b1a1a]">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- svelte-ignore a11y-click-events-have-key-events -->
      <!-- svelte-ignore a11y-no-static-element-interactions -->
      <div
        class="border-2 border-dashed border-[#eaeaea] rounded-xl p-8 text-center hover:border-[#ff7607] transition-colors cursor-pointer"
        on:click={() => photoFileInput?.click()}
        on:dragover|preventDefault
        on:drop|preventDefault={handlePhotoDropEvent}
      >
        <input
          type="file"
          bind:this={photoFileInput}
          accept="image/jpeg,image/png,image/gif,image/webp"
          multiple
          class="hidden"
          on:change={handlePhotoSelect}
        />

        {#if newPhotoPreviews.length > 0}
          <div class="grid grid-cols-4 gap-2 mb-4">
            {#each newPhotoPreviews as preview, i}
              <div class="relative aspect-square rounded overflow-hidden bg-gray-100">
                <img src={preview} alt="" class="w-full h-full object-cover" />
                <button
                  on:click|stopPropagation={() => removeNewPhoto(i)}
                  type="button"
                  class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center"
                >
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            {/each}
          </div>
        {/if}

        <p class="text-[#9b9b9b]">Click or drag photos here</p>
        <p class="text-xs text-[#9b9b9b] mt-1">JPEG, PNG, GIF, WebP · Max 10 MB each · Up to 20 at once</p>
      </div>

      <div class="flex justify-end gap-3 mt-6">
        <button on:click={closePhotoUpload} type="button" class="px-4 py-2 border border-[#eaeaea] rounded-lg text-[#1b1a1a] hover:bg-[#f9f9f9] font-medium text-sm">
          Cancel
        </button>
        <button
          on:click={uploadPhotos}
          disabled={isUploading || newPhotoFiles.length === 0}
          type="button"
          class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm disabled:opacity-50"
        >
          {isUploading ? 'Uploading…' : `Upload ${newPhotoFiles.length} photo${newPhotoFiles.length !== 1 ? 's' : ''}`}
        </button>
      </div>
    </div>
  </div>
{/if}

{#if showPhotoDeleteDialog}
  <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6">
      <h3 class="text-lg font-semibold text-[#1b1a1a] mb-2">Delete Photo</h3>
      <p class="text-[#9b9b9b] mb-2">Are you sure you want to delete this photo?</p>
      {#if photoToDelete?.alt_text}
        <p class="text-sm text-[#1b1a1a] mb-4 italic">"{photoToDelete.alt_text}"</p>
      {/if}
      <p class="text-xs text-red-500 mb-4">This cannot be undone. The file will be permanently deleted from storage.</p>
      <div class="flex justify-end gap-3">
        <button on:click={closePhotoDeleteDialog} type="button" class="px-4 py-2 border border-[#eaeaea] rounded-lg text-[#1b1a1a] hover:bg-[#f9f9f9] font-medium text-sm">Cancel</button>
        <button on:click={confirmDeletePhoto} type="button" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium text-sm">Delete</button>
      </div>
    </div>
  </div>
{/if}

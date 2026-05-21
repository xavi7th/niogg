<script>
  export let videos = [];

  let activeVideoId = null;
  let videoError = null;
  let retryCount = 0;
  const MAX_RETRIES = 3;
  let playerRef;
  let playerColumnHeight = 0;
  let playlistItemRefs = {};
  let videoEl;
  let shouldAutoplay = false;

  $: activeVideo = videos.find((v) => v.id === activeVideoId) ?? videos[0] ?? null;

  $: if (videos.length > 0 && !activeVideoId) {
    activeVideoId = videos[0].id;
  }

  $: if (activeVideoId) {
    videoError = null;
    retryCount = 0;
  }

  $: if (activeVideoId && playlistItemRefs[activeVideoId]) {
    playlistItemRefs[activeVideoId].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function setActiveVideo(video) {
    shouldAutoplay = true;
    activeVideoId = video.id;
    videoError = null;
    retryCount = 0;
    if (window.innerWidth < 1024) {
      playerRef?.scrollIntoView({ behavior: 'smooth' });
    }
  }

  function nextVideo() {
    shouldAutoplay = true;
    const idx = videos.findIndex((v) => v.id === activeVideoId);
    if (idx < videos.length - 1) {
      activeVideoId = videos[idx + 1].id;
    }
  }

  const handleVideoError = (e) => {
    const code = e.target?.error?.code ?? null;
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = { message: 'Error loading video.', retryable: retryCount < MAX_RETRIES };
    } else {
      videoError = { message: getVideoErrorMessage(code), retryable: false };
    }
  };

  const getVideoErrorMessage = (code) => {
    if (code === 2) return 'Network error loading video. Check your connection.';
    if (code === 3) return 'Video could not be decoded. File may be corrupted.';
    return 'An unexpected error occurred loading this video.';
  };

  const handleRetry = () => {
    if (retryCount < MAX_RETRIES) {
      retryCount++;
      videoError = null;
    }
  };

  function formatDuration(seconds) {
    if (!seconds) return '';
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
  }
</script>

<h2 class="text-2xl font-bold text-[#1b1a1a] mb-6">Videos</h2>

<div class="flex flex-col lg:flex-row gap-6 items-start">

  <!-- Player — 65% on desktop -->
  <div class="w-full lg:w-[65%]" bind:this={playerRef} bind:clientHeight={playerColumnHeight}>
    {#if activeVideo}
      <div class="bg-black rounded-xl overflow-hidden">
        {#if videoError}
          <div class="aspect-video flex flex-col items-center justify-center bg-gray-900 text-white p-6">
            <p class="text-red-400 mb-4">{videoError.message}</p>
            {#if videoError.retryable}
              <button
                on:click={handleRetry}
                type="button"
                class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
              >
                Retry ({MAX_RETRIES - retryCount} attempts remaining)
              </button>
            {/if}
          </div>
        {:else}
          {#key activeVideoId}
            <video
              bind:this={videoEl}
              controls
              class="w-full aspect-video"
              poster={activeVideo.thumbnail_url}
              on:loadeddata={() => { if (shouldAutoplay) videoEl?.play(); }}
              on:ended={nextVideo}
              on:error={handleVideoError}
            >
              <source src={activeVideo.video_url} type={activeVideo.mime_type || 'video/mp4'} />
            </video>
          {/key}
        {/if}
      </div>
      <div class="mt-4">
        <h3 class="text-xl font-semibold text-[#1b1a1a]">{activeVideo.title}</h3>
        {#if activeVideo.description}
          <p class="text-[#9b9b9b] mt-2 line-clamp-3">{activeVideo.description}</p>
        {/if}
      </div>
    {:else}
      <div class="bg-black rounded-xl aspect-video flex items-center justify-center">
        <p class="text-white opacity-50">No video selected</p>
      </div>
    {/if}
  </div>

  <!-- Playlist — 35% on desktop -->
  <div class="w-full lg:w-[35%]">
    <h4 class="font-medium text-[#1b1a1a] mb-3">Up Next</h4>
    <div
      class="space-y-1 overflow-y-auto pr-1"
      style="max-height: {playerColumnHeight > 0 ? playerColumnHeight + 'px' : '70vh'}"
    >
      {#each videos as video, i}
        <button
          bind:this={playlistItemRefs[video.id]}
          on:click={() => setActiveVideo(video)}
          type="button"
          class="w-full flex gap-3 p-2 rounded-lg transition-colors text-left border-l-4 {activeVideo?.id === video.id ? 'border-[#ff7607] bg-[#ff7607]/10' : 'border-transparent hover:bg-gray-50'}"
        >
          <div class="w-[60px] h-[45px] flex-shrink-0 bg-gray-100 rounded overflow-hidden">
            {#if video.thumbnail_url}
              <img src={video.thumbnail_url} alt={video.title} class="w-full h-full object-cover" loading="lazy" />
            {:else}
              <div class="w-full h-full flex items-center justify-center text-[#9b9b9b]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
              </div>
            {/if}
          </div>
          <div class="flex-1 min-w-0">
            <p
              class="text-sm leading-snug"
              class:font-semibold={activeVideo?.id === video.id}
              class:text-[#ff7607]={activeVideo?.id === video.id}
              class:font-medium={activeVideo?.id !== video.id}
              class:text-[#1b1a1a]={activeVideo?.id !== video.id}
            >
              {#if activeVideo?.id === video.id}▶ {/if}{i + 1}. {video.title}
            </p>
            {#if video.duration_seconds}
              <p class="text-xs text-[#9b9b9b] mt-0.5">{formatDuration(video.duration_seconds)}</p>
            {/if}
          </div>
        </button>
      {/each}
    </div>
  </div>

</div>

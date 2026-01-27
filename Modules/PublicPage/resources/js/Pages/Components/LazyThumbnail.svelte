<script>
  import { onMount, onDestroy } from 'svelte';

  let imageElement;
  let isVisible = false;
  let observer;

  export let src = '';
  export let alt = '';
  export let placeholder = '';

  onMount(() => {
    // Intersection Observer for lazy loading images
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          isVisible = true;
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '100px 0px', // Load 100px before entering viewport
      threshold: 0.01
    });

    if (imageElement) {
      observer.observe(imageElement);
    }
  });

  onDestroy(() => {
    if (observer) {
      observer.disconnect();
    }
  });
</script>

<div class="lazy-thumbnail-container" class:loaded={isVisible} {...$$restProps}>
  {#if isVisible}
    <img
      bind:this={imageElement}
      src={src}
      alt={alt}
      loading="lazy"
      class="lazy-thumbnail-image"
      decoding="async"
      style="will-change: opacity;"
    />
  {:else}
    <div class="placeholder">
      {#if placeholder}
        <img
          src={placeholder}
          alt="Loading placeholder"
          class="placeholder-image"
          loading="lazy"
        />
      {:else}
        <div class="placeholder-fallback">
          <svg width="50" height="50" viewBox="0 0 100 100" fill="none" stroke="#ccc" stroke-width="2">
            <circle cx="50" cy="50" r="48" fill="#f5f5f5" />
            <path d="M35 35 L65 65 M65 35 L35 65" stroke="#ccc" stroke-width="2" stroke-linecap="round" />
          </svg>
        </div>
      {/if}
    </div>
  {/if}
</div>

<style>
  .lazy-thumbnail-container {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    background: #f0f0f0;
    overflow: hidden;
    border-radius: 4px;
  }

  .lazy-thumbnail-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 4px;
  }

  .lazy-thumbnail-container.loaded .lazy-thumbnail-image {
    opacity: 1;
  }

  .placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
  }

  .placeholder-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.5;
  }

  .placeholder-fallback {
    color: #999;
  }

  /* Performance optimizations */
  .lazy-thumbnail-container {
    will-change: transform;
    transform: translateZ(0);
  }

  /* Smooth fade-in animation */
  .lazy-thumbnail-image {
    animation: fadeIn 0.3s ease forwards;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  /* Reduced motion support */
  @media (prefers-reduced-motion: reduce) {
    .lazy-thumbnail-image {
      animation: none;
      opacity: 1 !important;
    }
  }
</style>
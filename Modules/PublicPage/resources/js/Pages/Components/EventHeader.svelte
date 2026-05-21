<script>
  export let event = {};

  const getCategoryLabel = (category) => {
    if (!category) return '';
    return category
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };
</script>

<div class="event-header pt-80">
  <div class="header-top">
    <div class="icon-container">
      <div class="icon" aria-hidden="true">
        {event.icon}
      </div>
    </div>

    <div class="header-content">
      {#if event.category}
        <div class="category-badge">
          {getCategoryLabel(event.category)}
        </div>
      {/if}

      <h2 class="event-title">
        {event.name}
      </h2>

      {#if event.description}
        <p class="event-description px-2">
          {event.description}
        </p>
      {/if}

      {#if event.photos_count > 0 || event.videos_count > 0}
        <a href="/events/{event.slug}" class="media-counts">
          {#if event.photos_count > 0}
            <span class="media-count-item">
              <svg class="media-count-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              {event.photos_count}
            </span>
          {/if}
          {#if event.videos_count > 0}
            <span class="media-count-item">
              <svg class="media-count-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
              {event.videos_count}
            </span>
          {/if}
        </a>
      {/if}
    </div>
  </div>
</div>

<style>
  .event-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #ff7607;
  }

  .header-top {
    display: flex;
    align-items: flex-start;
    gap: 2rem;
    margin-bottom: 1rem;
  }

  .icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff7607, #ff9a3c);
    box-shadow: 0px 10px 40px 0px rgba(255, 118, 7, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 44px;
  }

  .header-content {
    /* display: flex; */
    flex-direction: column;
    justify-content: flex-start;
    flex: 1;
  }

  .event-title {
    font-family: Roboto, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    font-size: clamp(1.5rem, 5vw, 2.25rem);
    font-weight: 700;
    color: #1b1a1a;
    margin-bottom: 8px;
  }

  .category-badge {
    display: inline-block;
    background-color: #fff3e6;
    color: #ff7607;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    letter-spacing: 1.5px;
  }

  .event-description {
    font-size: clamp(0.875rem, 2vw, 1rem);
    color: #9b9b9b;
    line-height: 1.6;
    max-width: 800px;
    margin: 0;
  }

  .media-counts {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #9b9b9b;
    text-decoration: none;
    transition: color 0.2s ease;
  }

  .media-counts:hover {
    color: #ff7607;
  }

  .media-count-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .media-count-icon {
    width: 1rem;
    height: 1rem;
  }

  @media (max-width: 768px) {
    .header-top {
      flex-direction: column;
      align-items: center;
      gap: 1.5rem;
      text-align: center;
    }

    .icon {
      width: clamp(56px, 15vw, 80px);
      height: clamp(56px, 15vw, 80px);
      font-size: clamp(31px, 10vw, 44px);
    }

    .header-content {
      align-items: center;
    }

    .event-header {
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
    }
  }
</style>

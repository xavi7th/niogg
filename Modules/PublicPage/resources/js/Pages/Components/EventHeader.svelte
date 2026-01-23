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

<div class="event-header">
  <div class="icon-container">
    <div class="icon" aria-hidden="true">
      {event.icon}
    </div>
  </div>

  <h2 class="event-title">
    {event.name}
  </h2>

  {#if event.category}
    <div class="category-badge">
      {getCategoryLabel(event.category)}
    </div>
  {/if}

  {#if event.description}
    <p class="event-description">
      {event.description}
    </p>
  {/if}
</div>

<style>
  .event-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #ff7607;
  }

  .icon-container {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 1rem;
  }

  .icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: #ff7607;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
  }

  .event-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #1b1a1a;
    margin: 0 0 1rem 0;
  }

  .category-badge {
    display: inline-block;
    background-color: #fff3e6;
    color: #ff7607;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
  }

  .event-description {
    font-size: 1rem;
    color: #9b9b9b;
    line-height: 1.6;
    max-width: 800px;
    margin: 0;
  }

  @media (max-width: 768px) {
    .event-title {
      font-size: 1.5rem;
    }

    .icon {
      width: 60px;
      height: 60px;
      font-size: 36px;
    }

    .event-header {
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
    }
  }
</style>

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
    </div>
  </div>

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
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    flex: 1;
  }

  .event-title {
    font-size: clamp(1.5rem, 5vw, 2.25rem);
    font-weight: 700;
    color: #1b1a1a;
    margin: 0;
  }

  .category-badge {
    display: inline-block;
    background-color: #fff3e6;
    color: #ff7607;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
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

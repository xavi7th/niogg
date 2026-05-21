<script>
  export let onViewToggle = () => {};
</script>

<div class="media-showcase-hero">
  <div class="hero-content">
    <h2 class="hero-title">Explore Our Complete Video Collection</h2>
    <p class="hero-subtitle">Browse our complete video collection from all events</p>
    <button on:click={onViewToggle} class="hero-cta">
      View All Videos
    </button>
  </div>
</div>

<style>
  .media-showcase-hero {
    width: 100%;
    background: linear-gradient(135deg, rgba(255, 118, 7, 0.1) 0%, rgba(255, 118, 7, 0.05) 100%);
    padding: 3rem 1rem;
    margin-bottom: 2rem;
  }

  .hero-content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
  }

  .hero-title {
    font-size: clamp(1.5rem, 4vw, 2.5rem);
    font-weight: 700;
    color: #1b1a1a;
    margin: 0 0 0.75rem 0;
  }

  .hero-subtitle {
    font-size: clamp(0.875rem, 2vw, 1.125rem);
    color: #9b9b9b;
    margin: 0 0 1.5rem 0;
  }

  .hero-cta {
    background: linear-gradient(135deg, #ff7607 0%, #e66d06 100%);
    color: white;
    padding: 0.875rem 2.5rem;
    border: none;
    border-radius: 2rem;
    font-size: clamp(0.875rem, 2vw, 1rem);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 118, 7, 0.3);
    min-height: 52px;
    min-width: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .hero-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 118, 7, 0.4);
  }

  .hero-cta:active {
    transform: translateY(0);
  }

  @media (max-width: 768px) {
    .media-showcase-hero {
      padding: 2rem 0.75rem;
      margin-bottom: 1rem;
    }

    .hero-cta {
      width: 100%;
      max-width: 300px;
    }

    @media (hover: none) and (pointer: coarse) {
      .hero-cta:active {
        transform: scale(0.98);
        box-shadow: 0 2px 10px rgba(255, 118, 7, 0.3);
      }

      .hero-cta {
        -webkit-tap-highlight-color: transparent;
        user-select: none;
      }
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .hero-cta {
      transition: none !important;
    }

    .hero-cta:hover {
      transform: none !important;
    }
  }
</style>

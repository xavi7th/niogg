<script>
  import { getImgModule } from '@/helpers';

  /** @type {import('@root/types').Testimonial[]} */
  export let testimonials;
</script>

<section id="testimonial1" class="testimonial testimonial-1 pt-120 pb-70">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">

        <div class="heading heading-3 mb-50">
          <span class="heading__subtitle">Good Governance is a fundamental right of every citizenry</span>
          <h2 class="heading__title">What the world has to say about Good Governance</h2>
        </div>

        <div class="carousel owl-carousel carousel-dots carousel-dots-center" data-slide="2" data-slide-md="1"
          data-slide-sm="1" data-autoplay="false" data-nav="false" data-dots="true" data-space="50" data-loop="true"
          data-speed="800">

          {#each testimonials as t}
            <div class=" testimonial-item">
              <div class="testimonial__meta">
                <div class="testimonial__thumb">
                  {#await getImgModule(t.img_url, 'w=60&h=60')}
                    <p>loading...</p>
                  {:then src}
                    <enhanced:img src="{ src.default }" class="logo-light" alt="logo" />
                  {/await}
                </div>
                <h5 class="testimonial__meta-title">{t.name}</h5>
                <p class="testimonial__meta-desc">{t.country}</p>
              </div>
              <div class="testimonial__content">
                <p class="testimonial__desc">{t.testimonial}</p>
              </div>
            </div>
          {/each}
        </div>
      </div>
    </div>
  </div>
</section>

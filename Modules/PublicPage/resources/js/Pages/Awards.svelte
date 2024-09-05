<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Awards and Recognitions'>
  <li class="breadcrumb-item active" aria-current="page">Awards and Recognitions</li>
</PageTitle>

<section id="awards" class="awards pb-70 pt-0">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-10 offset-lg-1">
        <div class="heading heading-3 text-center mb-50">
          <span class="heading__subtitle">Good Goovernance takes effort</span>
          <h2 class="heading__title !text-3xl">Honoring Sacrifice, Inspiring Change: Rewarding Those Who Drive Good Governance.</h2>
        </div>
      </div>
    </div>
    <div class="row">

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/asuke-merit-award-2021.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content p-0 text-center">
            <h4 class="feature__title">Merit Award</h4>
            <!-- <p class="feature__desc">We believe that partnerships are fundamental to our growth. We'll seek the highest quality global forwarding services.</p> -->
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/niogg-business-icon-2021.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content p-0 text-center">
            <h4 class="feature__title">Outstanding Business Icon</h4>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/niogg-merit-award-2021.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content p-0 text-center">
            <h4 class="feature__title">Outstanding Leadership Icon Personality </h4>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/asuke-business-icon-2021.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content p-0 text-center">
            <h4 class="feature__title">Mentorship Icon Merit Award</h4>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

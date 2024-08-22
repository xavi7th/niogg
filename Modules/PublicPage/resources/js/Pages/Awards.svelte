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

<section id="awards" class="awards pb-70">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-10 offset-lg-1">
        <div class="heading heading-3 text-center mb-50">
          <span class="heading__subtitle">Good Goovernance takes effort</span>
          <h2 class="heading__title">Honoring Sacrifice, Inspiring Change: Rewarding Those Who Drive Good Governance.</h2>
        </div>
      </div>
    </div>
    <div class="row">

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/1.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Family Business Award</h4>
            <p class="feature__desc">We believe that partnerships are fundamental to our growth. We'll seek the
              highest quality global forwarding services.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/2.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Excellence in Exporting</h4>
            <p class="feature__desc">When we communicate, we are honest and direct. We won't make excuses, simply
              offer you a concrete solutions.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/3.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Top Leading Global Trade </h4>
            <p class="feature__desc">As a logistics solutions provider, we’ll monitor your logistical objectives and
              make sure all your logistical activities.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/4.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Sales Excellence Award</h4>
            <p class="feature__desc">We believe that partnerships are fundamental to our growth. We'll seek the
              highest quality global forwarding services.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/5.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Construction Excellence</h4>
            <p class="feature__desc">When we communicate, we are honest and direct. We won't make excuses, simply
              offer you a concrete solutions.</p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-4 col-lg-4">
        <div class="feature-item">
          <div class="feature__img">
            <enhanced:img class="img-fluid" src="@publicpage-template/images/awards/6.jpg?enhanced" alt="certificate"/>
          </div>
          <div class="feature__content">
            <h4 class="feature__title">Safety Award</h4>
            <p class="feature__desc">As a logistics solutions provider, we’ll monitor your logistical objectives and
              make sure all your logistical activities.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

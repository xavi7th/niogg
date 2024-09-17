<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { getImgUrl } from '@/helpers';
  import { page } from "@inertiajs/svelte";

  $: ({ app } = $page.props);
</script>

<div class="col-sm-12 col-md-12 col-lg-4 relative">
  <aside class="sidebar mb-30 mr-0 lg:!mr-5">

    <div class="widget widget-help bg-overlay bg-overlay-theme !p-5 !mb-2">
      <div class="bg-img"><img src="{getImgUrl('Modules/PublicPage/resources/images/src/sidebar/1.jpg')}" alt="background"></div>
      <div class="widget__content">
        <h3  class="text-white font-bold text-2xl text-center uppercase mb-0">{app.alt_name} Presents</h3>
      </div>
    </div>

    <div class="widget widget-posts !p-0 !pb-3 sponsor-imgs mb-50">
      <div class="widget__content">
        <div class="carousel owl-carousel carousel-dots carousel-dots-center" data-slide="1" data-slide-md="1" data-slide-sm="1" data-autoplay="true" data-nav="false" data-dots="true" data-space="0">

          <div class="widget-post-item">
            <div class="widget__post-img mb-0">
              <a href="#/">
                <!-- <img src="{getImgUrl('Modules/PublicPage/resources/images/src/sidebar/1.jpg')}" alt="background"> -->
                <enhanced:img src="@publicpage-assets/images/src/sidebar/niogg-flyer-neil-mccluskey.jpg?enhanced&w=600" class="w-full" alt="flyer thumb" />
              </a>
            </div>
          </div>

          <div class="widget-post-item">
            <div class="widget__post-img mb-0">
              <a href="#/">
                <enhanced:img src="@publicpage-assets/images/src/sidebar/niogg-flyer.jpg?enhanced&w=600" class="w-full" alt="flyer thumb" />
              </a>
            </div>
          </div>

          <div class="widget-post-item">
            <div class="widget__post-img mb-0">
              <a href="#/">
                <enhanced:img src="@publicpage-assets/images/src/sidebar/host-ushbebe.jpg?enhanced&w=600" class="w-full" alt="flyer thumb" />
              </a>
            </div>
          </div>

          <div class="widget-post-item">
            <div class="widget__post-img mb-0">
              <a href="#/">
                <enhanced:img src="@publicpage-assets/images/src/sidebar/niogg-launch-conference-qr-code.jpg?enhanced&w=600" class="w-full" alt="flyer thumb" />
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="widget mb-50">
      <h5 class="widget__title capitalize">Key Sponsors</h5>
      <div class="widget__content">
        <a href="#/" class="btn btn__block btn__hover3 mb-20">
          <enhanced:img src="@publicpage-assets/images/src/logo/logo-dark.png?enhanced" alt="pdf" />
        </a>
      </div>
    </div>

    <div class="widget widget-download">
      <div class="widget__content">
        <a href="#become-a-sponsor" class="btn btn__primary btn__block btn__hover3 text-center">
          <span class="grow text-xl font-bold uppercase">Become a Sponsor</span>
        </a>
      </div>
    </div>
  </aside>
</div>

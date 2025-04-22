<script>
  import { getImgUrl } from '@/helpers';
  export let appAltName = '';
</script>

<section id="banner2" class="banner banner-2 fancybox-white p-0">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6 inner-padding  bg-overlay bg-overlay-theme bg-parallax">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/images/placeholders/5.jpg') }" alt="background" />
        </div>
        <div class="heading heading-2 mb-50">
          <span class="heading__subtitle color-white">May 23, 2025</span>
          <h3 class="heading__title color-white">{appAltName} Achievers Icon/Merit Awards 2024</h3>
          <p class="heading__desc color-white">
            As part of its continued commitment to promoting accountability, transparency, and impactful leadership in Nigeria, the Nigeria Insight on Good Governance Civil Society Group ({appAltName}) is set to host the much-anticipated Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024 on May 23, 2025
          </p>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4">
        <div class="carousel-wrap">
          <div class="carousel owl-carousel carousel-dots" data-slide="1" data-slide-md="1" data-slide-sm="1"
            data-autoplay="true" data-nav="false" data-dots="true" data-space="0" data-loop="true" data-speed="700">
            <div class="item">
              <div class="cta"><a href="{ window.route('app.conferences.award.index') }" class="btn btn__white">View Details</a></div>
            </div>
            <div class="item">
              <div class="cta"><a href="{ window.route('app.awards') }" class="btn btn__white">View Awardees</a></div>
            </div>
          </div>
        </div>
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/banners/award123.jpg') }" alt="background" />
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .bg-img{
    background-position: bottom right;
  }

  .cta{
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .banner-2 .carousel{
    background-color: transparent;
  }
</style>

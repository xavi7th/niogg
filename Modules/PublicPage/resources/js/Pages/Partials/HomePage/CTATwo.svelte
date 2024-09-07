<script>
  import { getImgUrl } from '@/helpers';

  export let appAltName;
</script>

<section id="banner4" class="banner banner-4 pt-120 pb-120">
  <div class="bg-img">
    <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/banners/nigeria-region.jpg') }" alt="background" />
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-9 col-xl-7">
        <div class="banner-content">
          <div class="heading heading-3 heading-white mb-30 d-flex flex-column align-items-center">
            <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-light.png?enhanced&w=125&aspect=1:1" alt="author thumb" class="mb-30"/>
            <span class="heading__subtitle">Building The Future, Restoring The Past</span>
            <h2 class="heading__title">Nigeria Regional Office</h2>
            <p class="heading__desc">
              {appAltName} aims to contribute to good governance, through research, policy engagement and other activities that help promote sustainable socio-economic
              development in Africa’s largest economy and most populous country.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  #banner4 {
    position: relative;
    &::before{
      content: "";
      display: block;
      position: absolute;
      top: 0;
      bottom: 0;
      width: 100%;
      background: rgba(29, 26, 26, 0.56);
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(7.3px);
      -webkit-backdrop-filter: blur(7.3px);
      border: 2px solid rgba(29, 26, 26, 0.62);
      filter: grayscale(1);
    }
  }
</style>

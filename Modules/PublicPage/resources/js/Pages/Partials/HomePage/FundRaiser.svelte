<script>
  import { getImgUrl } from '@/helpers';
  export let appAltName = '';
</script>

<section id="banner2" class="banner banner-2 fancybox-white p-0">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6 inner-padding  bg-overlay bg-overlay-theme bg-parallax">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/backgrounds/2.jpg') }" alt="background" />
        </div>
        <div class="heading heading-2 mb-50">
          <span class="heading__subtitle color-white">Join us and Change Nigeria!</span>
          <h3 class="heading__title color-white">Help Us Raise Money For Our Awareness Drives</h3>
          <p class="heading__desc color-white">
            {appAltName}'s' awareness campaigns focus on educating youths and women about the importance of demanding government accountability and participating in the electoral process.
            These efforts are crucial for fostering a well-informed citizenry and promoting transparent governance. Your support will help us expand these campaigns and
            empower individuals to drive positive change in their communities.
          </p>
        </div>
        <div class="row">

          <div class="col-4 col-sm-4 col-md-4 col-lg-4">
            <div class="fancybox-item">
              <div class="fancybox__icon">
                <i class="icon-waste"></i>
              </div>
              <div class="fancybox__content">
                <h4 class="fancybox__title">Environmental <br> Sensitivity</h4>
                <div class="divider__line divider__sm divider__white"></div>
              </div>
            </div>
          </div>

          <div class="col-4 col-sm-4 col-md-4 col-lg-4">
            <div class="fancybox-item">
              <div class="fancybox__icon">
                <i class="icon-gears-1"></i>
              </div>
              <div class="fancybox__content">
                <h4 class="fancybox__title">Customized <br> Solutions</h4>
                <div class="divider__line divider__sm divider__white"></div>
              </div>
            </div>
          </div>

          <div class="col-4 col-sm-4 col-md-4 col-lg-4">
            <div class="fancybox-item">
              <div class="fancybox__icon">
                <i class="icon-management"></i>
              </div>
              <div class="fancybox__content">
                <h4 class="fancybox__title">Performance <br> Measures</h4>
                <div class="divider__line divider__sm divider__white"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4">
        <div class="carousel-wrap">
          <div class="carousel owl-carousel carousel-dots" data-slide="1" data-slide-md="1" data-slide-sm="1"
            data-autoplay="true" data-nav="false" data-dots="true" data-space="0" data-loop="true" data-speed="700">
            <div class="item">
              <div class="counter-item counters-white">
                <div class="counter__icon"><i class="icon-valve"></i></div>
                <h4 class="counter">173</h4>
                <p class="counter__subtitle">Drives</p>
                <p class="counter__desc">Good governance starts from the enlightenment grassroot stakeholders.</p>
              </div>
            </div>
            <div class="item">
              <div class="counter-item counters-white">
                <div class="counter__icon"><i class="icon-worker-1"></i></div>
                <h4 class="counter">12</h4>
                <p class="counter__subtitle">Employees</p>
                <p class="counter__desc">Passionate individuals like me and you convinced that change is possible.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/banners/awareness-drive.png') }" alt="background" />
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .bg-img{
    background-position: bottom right;
  }
</style>

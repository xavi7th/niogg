<script>
  import { getImgUrl } from '@/helpers';

  export let appAltName = ''
</script>

<section id="slider1" class="slider slider-1">
  <div class="carousel owl-carousel carousel-arrows" data-slide="1" data-slide-md="1" data-slide-sm="1"
    data-autoplay="true" data-nav="true" data-dots="false" data-space="0" data-loop="true" data-speed="3000"
    data-transition="fade" data-animate-out="fadeOut" data-animate-in="fadeIn">
    <div class="slide-item align-v-h bg-overlay">
      <div class="bg-img">
        <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/sliders/1.jpg') }" alt="slide" />
      </div>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6">
            <div class="slide__content">
              <h2 class="slide__title">Good Governance</h2>
              <p class="slide__desc">
                We need a country where citizens are empowered, governments are effective, businesses thrive, poverty is eradicated, and
                {appAltName} seeks to be the pre-eminent
                partner for advancing good governance in Nigeria.
              </p>
              <div class="cta">
                <a href={ window.route('app.about') } class="btn btn__white mr-40">About Us</a>
                <!-- <a class="btn btn__video popup-video" href="https://www.youtube.com/watch?v=nrJtHemSPW4">
                  <div class="video__player">
                    <span class="video__player-animation"></span>
                    <span class="video__player-animation video__player-animation-2"></span>
                    <span class="video__player-animation video__player-animation-3"></span>
                    <i class="fa fa-play"></i>
                  </div>Our Video!
                </a> -->
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-4 col-xl-3 offset-xl-2">
            <div class="slider__panel">
              <div class="slider__panel-icon">
                <i class="icon-manufacturing-plant"></i>
              </div>
              <h3 class="slider__panel-title">Our Vision</h3>
              <p class="slider__panel-desc">The Independence Of The Judiciary and the Integrity Our Government In The Campaign Against Corruption</p>
              <a href="#" class="btn btn__primary btn__link">
                <span>Explore</span>
                <i class="icon-arrow-right2"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="slide-item align-v-h bg-overlay">
      <div class="bg-img">
        <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/sliders/2.jpg') }" alt="slide" />
      </div>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6">
            <div class="slide__content">
              <h2 class="slide__title">Transparent Leadership </h2>
              <p class="slide__desc">
                Accountability is one of the bedrocks of representative government. Its absence will indeed lead to long-term instability.

                {appAltName} wants to give a voice to the sufferings of the populace through advocacy, engagement and education.
              </p>
              <div class="cta">
                <a href={ window.route('app.about') } class="btn btn__white mr-40">About Us</a>
                <!-- <a class="btn btn__video popup-video" href="https://www.youtube.com/watch?v=nrJtHemSPW4">
                  <div class="video__player">
                    <span class="video__player-animation"></span>
                    <span class="video__player-animation video__player-animation-2"></span>
                    <span class="video__player-animation video__player-animation-3"></span>
                    <i class="fa fa-play"></i>
                  </div>Our Video!
                </a> -->
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-4 col-xl-3 offset-xl-2">
            <div class="slider__panel">
              <div class="slider__panel-icon">
                <i class="icon-wheel"></i>
              </div>
              <h3 class="slider__panel-title">Our Mission</h3>
              <p class="slider__panel-desc">The Attainment Of Good Governace, Accountability, Efficient And Effective Electoral Systems In Nigeria</p>
              <a href="#" class="btn btn__primary btn__link">
                <span>Explore</span>
                <i class="icon-arrow-right2"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="slide-item align-v-h bg-overlay">
      <div class="bg-img">
        <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/sliders/3.jpg') }" alt="slide" />
      </div>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6">
            <div class="slide__content">
              <h2 class="slide__title">Institutional Collaborations</h2>
              <p class="slide__desc">
                At
                {appAltName}, we aim to transform lives through empowering individuals, particularly those affected by poverty, unemployment, and poor governance,
                 to hold their governments accountable for the provision of rubust schemes aimed at the greass root levels
              </p>
              <div class="cta">
                <a href={ window.route('app.about') } class="btn btn__primary mr-40">About Us</a>
                <!-- <a class="btn btn__video popup-video" href="https://www.youtube.com/watch?v=nrJtHemSPW4">
                  <div class="video__player">
                    <span class="video__player-animation"></span>
                    <span class="video__player-animation video__player-animation-2"></span>
                    <span class="video__player-animation video__player-animation-3"></span>
                    <i class="fa fa-play"></i>
                  </div>Our Video!
                </a> -->
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-12 col-lg-4 col-xl-3 offset-xl-2">
            <div class="slider__panel">
              <div class="slider__panel-icon">
                <i class="icon-container"></i>
              </div>
              <h3 class="slider__panel-title">Our Values</h3>
              <p class="slider__panel-desc">Education, Enlightenment &amp; Constructive Criticisms as Critical to the Attainment of Good Governace.</p>
              <a href="#" class="btn btn__primary btn__link">
                <span>Explore</span>
                <i class="icon-arrow-right2"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .cta{
    display: flex;
    justify-content: center;
  }

  .slider__panel{
    @media (max-width: 991px) {
      margin-top: 50px;
    }
  }
</style>

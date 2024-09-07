<script>
  import { getImgUrl } from '@/helpers';

  export let appAltName = '';
</script>


<section id="featuresListLayout2"
  class="features-list features-list-layout2 bg-overlay bg-overlay-gradient bg-parallax pt-120">
  <div class="bg-img">
    <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/placeholders/3.jpg') }" alt="background" />
  </div>
  <div class="container">
    <div class="row heading mb-30">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <span class="heading__subtitle">Why Choose Our Services?</span>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6">
        <h2 class="heading__title color-white">{appAltName} Good Governance Awareness Initiatives</h2>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6">
        <p class="heading__desc color-white">
          {appAltName}'s awareness initiatives demonstrate the benefits of good governance through entrepreneurship training, free medical treatments,
          charity donations, and recognizing individuals with outstanding contributions to community development.
        </p>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="feature-item feature-list-item">
          <div class="feature__content">
            <h4 class="feature__title">Entrepreneurship Training</h4>
            <p class="feature__desc">
              {appAltName}'s entrepreneurship training programs provide practical workshops and mentorship to help individuals start and grow successful businesses.
              We focus on essential skills like financial management, marketing, and leadership to foster innovation and self-reliance. Our goal is to
              empower participants to drive economic growth and create job opportunities in their communities.
            </p>
          </div>
        </div>
        <div class="feature-item feature-list-item">
          <div class="feature__content">
            <h4 class="feature__title">Free Medical Treatments</h4>
            <p class="feature__desc">
              {appAltName} provides free medical treatments through community health initiatives, offering essential care and services to underserved populations.
              Our goal is to enlighten underserved communities on the need to strive for good governance that can provide access to healthcare and promote overall well-being,
              addressing critical health needs and reducing barriers to treatment for those in need.
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="feature-item feature-list-item">
          <div class="feature__content">
            <h4 class="feature__title">Charity Donations</h4>
            <p class="feature__desc">
              {appAltName}'s charity donations aim to provide vital support to underserved communities, showcasing the benefits that good governance can bring. By addressing immediate
              needs and alleviating hardship, we highlight the disparities caused by poor governance and emphasize the importance of striving for accountable leadership
               to ensure that all citizens receive the support they deserve.
            </p>
          </div>
        </div>
        <div class="feature-item feature-list-item">
          <div class="feature__content">
            <h4 class="feature__title">Awards and Recognitions</h4>
            <p class="feature__desc">
              {appAltName}'s awards and recognitions honor individuals who have made significant contributions to peace and good governance, often overlooked because of corruption.
              By celebrating their achievements, we emphasize the gap left by poor leadership and stress the need for everyone to demand effective and accountable governance
              to ensure that such contributions are duly recognized and supported.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .heading__subtitle{
    color: #fff;
  }
</style>

<script>
  export let appName = '', appAltName = ''
</script>

<section id="about2" class="about about-2 pt-0 pb-90">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12 col-xl-9 offset-xl-1 ">
        <div class="heading heading-3 mb-50">
          <span class="heading__subtitle">ABOUT US</span>
          <h2 class="heading__title">{appName}</h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="about__text mt-30">
          <p>{appAltName} is a voice and accountability CSOS dedicated to promoting good governance in Nigeria.</p>
          <p>Nigeria Insight on Governance Ltd/GTE ({appAltName}) focuses on youth leadership development,
            aiming to empower young Nigerians to take active roles in governance. {appAltName} is committed to fostering accountability and transparency in both
            government and public institutions. It also seeks to ensure an effective and credible electoral system in Nigeria. Additionally, {appAltName} emphasizes
             the importance of citizen education, guiding them to actively participate in governance processes. The organization upholds the constitution and advocates
             for its principles. Ultimately, {appAltName} contributes to the overall societal development of Nigeria through its initiatives.</p>
        </div>
        <div class="signature mb-30">
          <enhanced:img src="@publicpage-template/images/about/singnture.png?enhanced" class="signature" alt="signature"/>
        </div>
      </div>
      <div class="col-sm-12 col-md-8 col-lg-5 offset-lg-1">
        <div class="about__img">
          <enhanced:img src="@publicpage-template/images/about/active-participation.jpg?enhanced&h=420&w=390&position=top&aspect=13:14" class="img-fluid" alt="about"/>
          <a href={ window.route('app.contact') } class="btn btn__primary popup-video">
            <span class="mr-2">Participate!</span><i class="fa fa-play"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

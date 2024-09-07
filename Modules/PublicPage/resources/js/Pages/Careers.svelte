<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Careers'>
  <li class="breadcrumb-item"><a href="#/">About Us</a></li>
  <li class="breadcrumb-item active" aria-current="page">Careers</li>
</PageTitle>

<section id="about1" class="about about-1 pt-80 pb-0">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-7">
        <div class="carousel-wrap">
          <div class="carousel owl-carousel carousel-arrows" data-slide="1" data-slide-md="1" data-slide-sm="1" data-autoplay="true" data-nav="true" data-dots="false" data-space="0" data-loop="true" data-speed="700">
            <enhanced:img class="img-fluid" src="@publicpage-assets/images/src/backgrounds/equal-opportunity.jpg?enhanced&w=670&aspect=670:450" alt="about thumb"/>
            <enhanced:img class="img-fluid" src="@publicpage-assets/images/src/backgrounds/one-nigeria.png?enhanced&w=670&aspect=670:450&position=top" alt="about thumb"/>
          </div>
          <div class="counter-item counters-white">
            <div class="counter__icon"><i class="icon-worker-1"></i></div>
            <h4 class="counter">18</h4>
            <p class="counter__subtitle">Employees</p>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-5">
        <div class="heading heading-2 mt-50">
          <span class="heading__subtitle">Work With Us</span>
          <h2 class="heading__title">Join Us in Shaping the Future of Governance in Nigeria!</h2>
          <p class="heading__desc">
            {app.alt_name}  is an equal opportunity employer. {app.alt_name}  does not discriminate on the basis of race, religion, colour, sex, age, non-disqualifying physical
            or mental disability, state of origin, or  any other basis covered by appropriate law. All employment is decided on the basis of qualifications, merit and business need.
          </p>
        </div>
        <a href="{ window.route('app.about') }" class="btn btn__default btn__bordered">More About Us</a>
      </div>
    </div>
  </div>
</section>



<section id="careers" class="careers pt-120">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-10 offset-lg-1">
        <div class="heading heading-3 text-center mb-40">
          <h2 class="heading__title">Career Opportunities</h2>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="carousel owl-carousel carousel-dots" data-slide="3" data-slide-md="2" data-slide-sm="1"
          data-autoplay="true" data-nav="false" data-dots="true" data-space="30" data-loop="true" data-speed="800">

          <div class=" job-item">
            <div class="job__meta">
              <span class="job__type">Part Time</span>
              <span class="job__location">Warri, Delta State</span>
            </div>
            <h4 class="job__title">Volunteer Staff</h4>
            <p class="job__desc">
              Are you passionate about making a difference in society? Do you believe in the power of good governance, transparency, and accountability?
              {app.alt_name} invites you to join our team of dedicated volunteers working to transform governance across Nigeria.
            </p>
            <a href="mailto:{app.alt_email}" class="btn btn__secondary btn__link" target="_blank">
              <span>Apply Now</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>

          <div class=" job-item">
            <div class="job__meta">
              <span class="job__type">Part Time</span>
              <span class="job__location">Nationwide</span>
            </div>
            <h4 class="job__title">State Co-ordinators</h4>
            <p class="job__desc">
              NIOGG is seeking passionate individuals to serve as State Coordinators, responsible for leading and coordinating all NIOGG activities within their respective states.
              As a State Coordinator, you will work closely with our head office in Delta State to drive local initiatives and make a meaningful impact on
              governance and community development.
            </p>
            <a href="mailto:{app.alt_email}" class="btn btn__secondary btn__link">
              <span>Apply Now</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>

          <div class=" job-item">
            <div class="job__meta">
              <span class="job__type">Part Time</span>
              <span class="job__location">Nationwide</span>
            </div>
            <h4 class="job__title">Election Observers</h4>
            <p class="job__desc">
              Join NIOGG in safeguarding Nigeria’s democracy as an Election Observer! We are seeking passionate volunteers to help ensure credible and transparent elections
              while educating the public on the vital importance of fair and democratic processes.
            </p>
            <a href="mailto:{app.alt_email}" class="btn btn__secondary btn__link">
              <span>Apply Now</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>

          <div class=" job-item">
            <div class="job__meta">
              <span class="job__type">Part Time</span>
              <span class="job__location">Delta State</span>
            </div>
            <h4 class="job__title">Volunteer Trainers</h4>
            <p class="job__desc">
              Empower the next generation by becoming a Volunteer Trainer with NIOGG! We are looking for enthusiastic individuals to join our team and contribute
              to our skill acquisition and empowerment programs, helping to equip Nigerians with the tools they need to succeed and thrive.
            </p>
            <a href="mailto:{app.alt_email}" class="btn btn__secondary btn__link">
              <span>Apply Now</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

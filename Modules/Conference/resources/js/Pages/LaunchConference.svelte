<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { getImgUrl } from '@/helpers';
  import { page } from "@inertiajs/svelte";
  import BecomeAPartner from './BecomeAPartner.svelte';
  import LaunchPageAside from './LaunchPageAside.svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import ConferenceSpeakers from './LaunchConferenceSpeakers.svelte';
  import LaunchConferenceRegistrationForm from './Partials/LaunchConferenceRegistrationForm.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Nigeria Leadership Quest for Economic Growth/ Business Summit Gala Night Award'>
  <li class="breadcrumb-item active capitalize" aria-current="page">Nigeria leadership quest for economic growth Conference</li>
</PageTitle>

<section id="textContentSection" class="text-content-section pt-0 xl:pt-50">
  <div class="container max-w-screen-xl">
    <div class="row">
      <LaunchPageAside />

      <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="text__block">
          <h2 class="text__block-title text-2xl md:text-3xl lg:text-4xl text-gray-800">Be part of the conversation</h2>
          <p class="text__block-desc">Nigeria Insight on Good Governance (NIOGG) is proud to announce the <b>The Nigeria Leadership Quest For For Economic Growth & Business Summit / Gala Night Award</b>, a groundbreaking event aimed at exploring the pivotal role of leadership in driving sustainable economic growth. Set to bring together visionary leaders, policymakers, industry experts, and youth advocates, this conference is poised to be a game-changer in shaping Nigeria’s future.</p>
          <p class="text__block-desc">This event scheduled for 6th November, 2024 at Eko Hotels and Suites, Lagos, Nigeria, time for the morning session 9:00am to 1:00pm and for the evening session BUSINESS SUMMIT GALA NIGHT AWARD 6:00pm to 10:00pm. By partnering with us, your Company/Brand can play a significant role in advancing transparency, accountability, and effective leadership in our nation.</p>
          <p class="text__block-desc">This high-profile gathering will delve into innovative solutions to Nigeria’s economic challenges, spotlighting the importance of accountable governance and youth leadership in national development. The conference provides a unique platform for stakeholders across sectors—governmental bodies, private industries, civil society, and financial institutions—to exchange ideas and strategies that can catalyze economic growth and ensure a prosperous future for all Nigerians.</p>
          <p class="text__block-desc">With keynote speakers, insightful panels, and interactive discussions, approximately 250 attendees, including key stakeholders from government, the private sector, civil society, and the media, will gain valuable insights into emerging opportunities in governance, leadership, and economic policy. NIOGG invites public and private organizations to sponsor this impactful event, contributing to the shared goal of fostering good governance and economic development in Nigeria.</p>
          <p class="text__block-desc">Join us at the Nigeria Leadership Quest for Economic Growth Conference and be part of the movement shaping the future of Nigeria. Your sponsorship and participation will not only help propel the event’s success but also support NIOGG's mission to build a more accountable, transparent, and economically thriving Nigeria.</p>
          <div class="video-1 bg-overlay mb-25 ring-2 ring-offset-4 ring-slate-200 hover:ring-slate-300 hover:ring-offset-8 scale-95 hover:scale-100 transition-all duration-700 ease-in-out">
            <div class="bg-img"><img src="{getImgUrl('Modules/PublicPage/resources/images/src/video/conference-flyer.jpg')}" alt="background"></div>
            <div class="video__btn text-center">
              <a class="popup-video" href="https://youtu.be/5h2SAVvhcDc?si=TeN3o7BpBAFedM_C">
                <span class="video__player-animation"></span>
                <span class="video__player-animation video__player-animation-2"></span>
                <span class="video__player-animation video__player-animation-3"></span>
                <div class="video__player">
                  <i class="fa fa-play"></i>
                </div>
              </a>
            </div>
          </div>
          <p class="text__block-desc">We aim at the Nigeria Leadership Quest for Economic Growth Conference, to provide an atmosphere where you’ll engage, learn, inspire, experience, and network with leaders committed to shaping a prosperous future for Nigeria.</p>
        </div>

        <div class="text__block-2">
          <h5 class="text__block-title">ENGAGE</h5>
          <p class="text__block-desc">Join thought leaders and change-makers as they engage in insightful discussions on Nigeria’s economic and leadership challenges. Your voice matters—be part of the dialogue that shapes policies and reforms for a better Nigeria.</p>
        </div>

        <div class="text__block-2">
          <h5 class="text__block-title">LEARN</h5>
          <p class="text__block-desc">Gain in-depth knowledge from experts across sectors who will share cutting-edge solutions for economic growth and governance. Leave equipped with actionable strategies to drive change in your community or organization.</p>
        </div>

        <div class="text__block-2">
          <h5 class="text__block-title">INSPIRE</h5>
          <p class="text__block-desc">Hear success stories from transformative leaders who have made a tangible impact in governance and economic development. Be inspired to take bold steps in your leadership journey and contribute to Nigeria’s brighter future.</p>
        </div>

        <div class="text__block-2">
          <h5 class="text__block-title">EXPERIENCE</h5>
          <p class="text__block-desc">Immerse yourself in a one-of-a-kind event filled with interactive sessions, keynote presentations, and dynamic workshops. Experience firsthand the exchange of ideas and the energy of a community committed to progress.</p>
        </div>

        <div class="text__block-2">
          <h5 class="text__block-title">GROW</h5>
          <p class="text__block-desc">Connect with a diverse group of industry leaders, policymakers, and innovators who share your passion for driving economic growth and accountability. Build relationships that can foster future collaborations and opportunities for both personal and national development.</p>
        </div>

        <div class="text__block !my-20">
          <h2 class="text__block-title text-2xl md:text-3xl lg:text-4xl text-gray-800">Event Highlights:</h2>
          <p class="text__block-desc mb-0 ml-10"> •	Opening remarks </p>
          <p class="text__block-desc mb-2 ml-10"> •	Keynote Speeches by prominent figures in governance and public policy. </p>
          <p class="text__block-desc mb-2 ml-10"> •	Networking Sessions allowing participants to connect and collaborate. </p>
          <p class="text__block-desc mb-2 ml-10"> •	Refreshment for morning section </p>
          <p class="text__block-desc mb-2 ml-10"> •	Red Carpet Reception </p>
          <p class="text__block-desc mb-2 ml-10"> •	Dinner </p>
          <p class="text__block-desc mb-2 ml-10"> •	Award ceremony </p>
          <p class="text__block-desc mb-2 ml-10"> •	Entertainment </p>
          <p class="text__block-desc mb-2 ml-10"> •	Broad business review </p>
          <p class="text__block-desc mb-2 ml-10"> •	Refreshment for evening section </p>
        </div>

        <BecomeAPartner />

        <LaunchConferenceRegistrationForm />

        <!-- <img src="assets/images/charts/1.png" alt="chart" class="img-fluid mb-50"> -->

        <!-- <div class="testimonial-wrap-panel mb-60">
          <div class="carousel owl-carousel carousel-dots crousel-dots-center" data-slide="1" data-slide-md="1"
            data-slide-sm="1" data-autoplay="false" data-nav="false" data-dots="true" data-space="0"
            data-loop="true" data-speed="800">

            <div class=" testimonial-item">
              <div class="testimonial__meta">
                <div class="testimonial__thumb">
                  <img src="assets/images/testimonials/thumbs/3.jpg" alt="author thumb">
                </div>
                <h5 class="testimonial__meta-title">Martin Hope</h5>
                <p class="testimonial__meta-desc">Pro Dust</p>
              </div>
              <div class="testimonial__content">
                <p class="testimonial__desc">I’ve seen great companies serving industry solutions in my career.
                  But
                  not to the point where you feel that comfort and trust that we get with Industic Company.</p>
                <div class="testimonial__rating">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div>
              </div>
            </div>

            <div class=" testimonial-item">
              <div class="testimonial__meta">
                <div class="testimonial__thumb">
                  <img src="assets/images/testimonials/thumbs/1.jpg" alt="author thumb">
                </div>
                <h5 class="testimonial__meta-title">John Peter</h5>
                <p class="testimonial__meta-desc">Dustic</p>
              </div>
              <div class="testimonial__content">
                <p class="testimonial__desc">Expertly trained team members who take the extra step and go the
                  extra
                  mile, all to fulfill our promise, deliver innovative and dynamic solutions to our customers!</p>
                <div class="testimonial__rating">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div>
              </div>
            </div>
          </div>
        </div> -->

        <ConferenceSpeakers />
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  :global{
    .collapse{
      visibility: inherit !important;
    }

    .video-1.bg-overlay{
      width: 700px;
      height: 700px;
      margin: auto;
      border-radius: 1rem;

      &::before{
        border-radius: 1rem;
        transition: background-color ease-out 500ms;
      }

      &:hover{
        &::before{
          background-color: rgba(27, 26, 26, 0.85);
        }
      }

      .video__btn{
        top: calc(50% - 37.5px);
      }
    }
  }
</style>

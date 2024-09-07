<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import { getImgUrl } from '@/helpers';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Nigeria leadership quest for economy growth Conference'>
  <li class="breadcrumb-item active" aria-current="page">Nigeria leadership quest for economy growth Conference</li>
</PageTitle>

<section id="textContentSection" class="text-content-section pt-0 xl:pt-50">
  <div class="container max-w-screen-xl">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-4 relative">
        <aside class="sidebar mb-30 mr-0 lg:!mr-5">

          <div class="widget widget-help bg-overlay bg-overlay-theme !p-5 !mb-2">
            <div class="bg-img"><img src="{getImgUrl('Modules/PublicPage/resources/images/src/sidebar/1.jpg')}" alt="background"></div>
            <div class="widget__content">
              <h3  class="text-white font-bold text-2xl text-center uppercase mb-0">{app.alt_name} Presents</h3>
            </div>
          </div>

          <div class="sponsor-imgs mb-50">
            <!-- <img src="{getImgUrl('Modules/PublicPage/resources/images/src/sidebar/1.jpg')}" alt="background"> -->

            <enhanced:img src="@publicpage-assets/images/src/sidebar/niogg-flyer.jpg?enhanced" class="w-full" alt="background">
          </div>

          <div class="widget">
            <h5 class="widget__title capitalize">Key Sponsors</h5>
            <div class="widget__content">
              <a href="#" class="btn btn__block btn__hover3 mb-20">
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

      <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="text__block">
          <h2 class="text__block-title text-2xl md:text-3xl lg:text-4xl text-gray-800">Be part of the conversation</h2>
          <p class="text__block-desc">Nigeria Insight on Good Governance (NIOGG) is proud to announce the <b>The Nigeria Leadership Quest For For Economic Growth & Business Summit / Gala Night Award</b>, a groundbreaking event aimed at exploring the pivotal role of leadership in driving sustainable economic growth. Set to bring together visionary leaders, policymakers, industry experts, and youth advocates, this conference is poised to be a game-changer in shaping Nigeria’s future.</p>
          <p class="text__block-desc">This high-profile gathering will delve into innovative solutions to Nigeria’s economic challenges, spotlighting the importance of accountable governance and youth leadership in national development. The conference provides a unique platform for stakeholders across sectors—governmental bodies, private industries, civil society, and financial institutions—to exchange ideas and strategies that can catalyze economic growth and ensure a prosperous future for all Nigerians.</p>
          <p class="text__block-desc">With keynote speakers, insightful panels, and interactive discussions, attendees will gain valuable insights into emerging opportunities in governance, leadership, and economic policy. NIOGG invites public and private organizations to sponsor this impactful event, contributing to the shared goal of fostering good governance and economic development in Nigeria.</p>
          <p class="text__block-desc">Join us at the Nigeria Leadership Quest for Economic Growth Conference and be part of the movement shaping the future of Nigeria. Your sponsorship and participation will not only help propel the event’s success but also support NIOGG's mission to build a more accountable, transparent, and economically thriving Nigeria.</p>
          <div class="video-1 bg-overlay mb-25">
            <div class="bg-img"><img src="{getImgUrl('Modules/PublicPage/resources/images/src/video/1.jpg')}" alt="background"></div>
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

        <div class="row relative bg-overla bg-overlay-them bg-gray-100 px-2 py-16 !mb-20">
          <div class="col-sm-12 col-lg-4 md:flex lg:flex-col">
            <div class="contact-panel mb-40 w-full md:w-2/5 lg:w-full">
              <h6 class="contact__panel-title">Bank Details for Manual Registration</h6>
              <ul class="contact__panel-list list-unstyled">
                <li class="capitalize"><i class="fa fa-bank"></i><span>2045475254</span></li>
                <li class="capitalize"><i class="fa fa-user"></i><span>Nigeria insight on good governance</span></li>
                <li class="capitalize"><i class="fa fa-bank"></i><span>First Bank Nigeria</span></li>
              </ul>
            </div>
            <div class="contact-panel md:mt-6 lg:mt-0 mb-40 w-full md:w-3/5 lg:w-full">
              <ul class="contact__panel-list contact__panel-list-2 list-unstyled">
                <li>
                  <span class="text__link">
                    Send us your payment receipt on <span class="font-bold color-theme">{app.phone}</span> and we will get back to you within 24 hours
                    with your event registration details or you can register automatically using the form<span class="lg:hidden">&nbsp; below</span>.
                  </span>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-sm-12 col-lg-8" id="become-a-sponsor">
            <div class="request-quote-panel">
              <div class="request__form mb-0 !basis-full">
                <div class="request__form-body">
                  <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                      <h4 class="request__form-title">Book Your Reservation</h4>
                      <p class="request__form-desc">Fill the form below and follow the steps to complete your registration automatically.</p>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <input type="email" class="form-control" placeholder="We will send your registration details via mail to you">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone">
                      </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 text-right">
                      <button class="btn btn__secondary">Register Now</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>



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


        <h2 class="text-4xl text-gray-800">Key Benefits</h2>
        <div id="accordion" class="mb-60 testimonial-wrap-panel !p-4 md:!p-16">
          <div class="accordion-item opened">
            <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse3">
              <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Become a sponsor</a>
            </div>
            <div id="collapse3" class="collapse show" data-parent="#accordion">
              <div class="accordion__item-body">
                <p>
                  Support the Nigeria Leadership Quest for Economic Growth Conference by becoming a sponsor and align your brand with a movement dedicated to driving economic
                  development and good governance in Nigeria. Sponsorship levels offer varying degrees of visibility, with benefits such as logo placement,
                  speaking opportunities, and networking access. Whether you choose a tier that grants exhibitor privileges or prefer to make a quieter impact,
                  your sponsorship will not only elevate your brand but also help NIOGG advance its mission of fostering accountability, transparency, and youth leadership in Nigeria.
                </p>
              </div>
            </div>
          </div>

          <div class="accordion-item">
            <div class="accordion__item-heade" data-toggle="collapse" data-target="#collapse1">
              <a class="accordion__item-title text-xl font-semibold uppercase" href="#">Become an exhibitor</a>
            </div>
            <div id="collapse1" class="collapse" data-parent="#accordion">
              <div class="accordion__item-body">
                <p>
                  Showcase your brand, products, or services to a highly engaged audience of industry leaders, policymakers, and innovators by becoming an exhibitor at the
                  Nigeria Leadership Quest for Economic Growth Conference. This exclusive opportunity allows you to directly interact with attendees, highlight your contributions to
                  Nigeria’s economic and governance landscape, and forge valuable connections that can propel your business or organization forward. As an exhibitor,
                  you’ll gain unparalleled visibility, position your brand as a leader in the space, and be at the forefront of the conversations shaping Nigeria's future.
                </p>
              </div>
            </div>
          </div>
        </div>

        <h2 class="text-4xl text-gray-800">Our Esteemed Speakers</h2>
        <div class="projects-carousel-2 carousel owl-carousel carousel-arrows" data-slide="3" data-slide-md="2"
          data-slide-sm="1" data-autoplay="true" data-nav="true" data-dots="false" data-space="30" data-loop="true"
          data-speed="800">
          <div class="project-item">
            <div class="project__img">
              <enhanced:img src="@publicpage-assets/images/src/placeholders/1.jpg?enhanced" class="w-full !rounded-3xl" alt="project-img" />
            </div>
            <div class="project__content">
              <h4 class="project__title"><a href="#">John Doe</a></h4>
              <div class="project__cat">
                <a href="#">Chemicals</a><a href="#">Oil & Gas</a>
              </div>
            </div>
          </div>
          <div class="project-item">
            <div class="project__img">
              <enhanced:img src="@publicpage-assets/images/src/placeholders/2.jpg?enhanced" class="w-full !rounded-3xl" alt="project-img" />
            </div>
            <div class="project__content">
              <h4 class="project__title"><a href="#">Sarah Pinkett</a></h4>
              <div class="project__cat">
                <a href="#">Analystics</a><a href="#">Optimization</a>
              </div>
            </div>
          </div>
          <div class="project-item">
            <div class="project__img">
              <enhanced:img src="@publicpage-assets/images/src/placeholders/3.jpg?enhanced" class="w-full !rounded-3xl" alt="project-img" />
            </div>
            <div class="project__content">
              <h4 class="project__title"><a href="#">Allen Poe</a></h4>
              <div class="project__cat">
                <a href="#">Solar</a><a href="#">Energy Initiatives</a>
              </div>
            </div>
          </div>
          <div class="project-item">
            <div class="project__img">
              <enhanced:img src="@publicpage-assets/images/src/placeholders/4.jpg?enhanced" class="w-full !rounded-3xl" alt="project-img" />
              <a href="#" class="zoom__icon"></a>
            </div>
            <div class="project__content">
              <h4 class="project__title"><a href="#">Winston Burklin</a></h4>
              <div class="project__cat">
                <a href="#">Analystics</a><a href="#">Optimization</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  :global{
    .project-item{

      .project__img{

        &:after{
          border-radius: 1.5rem;
        }
      }
    }

    .collapse{
      visibility: inherit !important;
    }
  }

  #become-a-sponsor{
    scroll-margin-top: 150px;

    @media (max-width: 991px) {
      scroll-margin-top: 250px;
    }

    @media (max-width: 540px) {
      scroll-margin-top: 400px;
    }
  }
</style>

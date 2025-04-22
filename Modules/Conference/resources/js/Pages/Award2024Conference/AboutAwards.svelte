<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import LaunchVideo from '../LaunchVideo.svelte';
  import BecomeAPartner from '../BecomeAPartner.svelte';
  import AwardPageAside from '../Partials/AwardPageAside.svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import ConferenceSpeakers from '../LaunchConferenceSpeakers.svelte';
  import LaunchConferenceRegistrationForm from '../Partials/LaunchConferenceRegistrationForm.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024'>
  <li class="breadcrumb-item active capitalize" aria-current="page">Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024</li>
</PageTitle>

<section id="textContentSection" class="text-content-section pt-0 xl:pt-50">
  <div class="container max-w-screen-xl">
    <div class="row">
      <AwardPageAside />

      <div class="col-sm-12 col-md-12 col-lg-8">
        <div class="text__block">
          <h2 class="text__block-title text-2xl md:text-3xl lg:text-4xl text-gray-800">Celebrating Icons of Integrity and Leadership</h2>
          <p class="text__block-desc">As part of its continued commitment to promoting accountability, transparency, and impactful leadership in Nigeria, the Nigeria Insight on Good Governance Civil Society Group (NIOGG) is set to host the much-anticipated <b>Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024</b> on May 23, 2025.</p>
          <p class="text__block-desc">This prestigious award ceremony is designed to honor individuals, institutions, and stakeholders who have demonstrated exceptional dedication to good governance, youth empowerment, ethical leadership, and the advancement of democratic values in Nigeria.</p>
          <h6>About the Awards</h6>
          <p class="text__block-desc">
            The Nigeria Insight on Good Governance Achievers Icon/Merit Awards is a hallmark event that celebrates excellence and commitment to nation-building. The 2024 edition will spotlight notable achievers from various sectors including public service, education, health, community development, civil society, business, media, and more.
            Each award recipient has been carefully selected based on their tangible contributions to governance and their unwavering commitment to ethical leadership and public accountability.
          </p>
          <h6>Why it Matters</h6>
          <p class="text__block-desc">In a time where the need for credible leadership and active civic engagement is more crucial than ever, this event serves as both recognition and motivation. It reinforces the importance of upholding democratic principles and encourages more citizens to contribute meaningfully to Nigeria’s growth and development.</p>
          <h6>Looking Ahead</h6>
          <p class="text__block-desc">As NIOGG continues its advocacy and developmental work, the Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024 will not only serve as a platform for recognition but also as a rallying point for continued progress, unity, and shared vision for a better Nigeria.</p>

          <!-- <LaunchVideo /> -->

          <p class="text__block-desc">We aim at the Nigeria Insight on Good Governance Achievers Icon/Merit Awards 2024 conference, to provide an atmosphere where you’ll engage, learn, inspire, experience, and network with leaders committed to shaping a prosperous future for Nigeria.</p>
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
          <p class="text__block-desc mb-2 ml-10"> •	Refreshment for evening session </p>
        </div>

        <div class="widget widget-download">
          <div class="widget__content">
            <a href="{ window.route('app.conferences.award.awardees') }" class="btn btn__primary btn__block btn__hover3 text-center"><span class="grow text-xl font-bold uppercase">View Awardees</span></a>
          </div>
        </div>

        <BecomeAPartner />

        <!-- <LaunchConferenceRegistrationForm /> -->

        <!-- <ConferenceSpeakers /> -->
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  :global{
    .collapse{
      visibility: inherit !important;
    }
  }
</style>

<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { getImgUrl } from '@/helpers';
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Vision and Values'>
  <li class="breadcrumb-item"><a href="#/">About Us</a></li>
  <li class="breadcrumb-item active" aria-current="page">Vision and Values</li>
</PageTitle>

<section id="banner3" class="banner banner-3 mt-[-6rem] pt-24 bg-gray-100">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6 bg-gray-100 text-gray-900 my-9">
        <div class="inner-padding">
          <div class="heading heading-2mb-30">
            <h2 class="heading__title">What does {app.alt_name} Exist?</h2>
            <p class="heading__desc">
              {app.alt_name} exists to enhance governance in Nigeria by advocating for transparency and accountability as the fundamental pillars of national development.
              We are committed to strengthening the rule of law and empowering citizens to actively participate in governance, ensuring that executive power
              is held in check. Our mission is rooted in the belief that better governance leads to improved economic, social, and environmental outcomes, ultimately
              resulting in greater well-being for all Nigerians.
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4 pt-100 pb-100 my-9 rounded">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/banners/enlightenment-campaigns.jpg') }" alt="background" />
        </div>
      </div>




      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4 pt-100 pb-100 my-9 rounded">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/banners/fight-for-good-government.jpg') }" alt="background" />
        </div>
      </div>

      <div class="col-sm-12 col-md-12 col-lg-6 bg-gray-100 text-gray-900 my-9">
        <div class="inner-padding">
          <div class="heading heading-2mb-30">
            <h2 class="heading__title">What Does {app.alt_name} Do?</h2>
            <p class="heading__desc">
              {app.alt_name} conducts high-quality, demand-driven research to support the creation of more inclusive and effective governance in Nigeria.
              Our research provides an evidence base for developing practical policies that improve efficiency and accountability in governance.
              We are deeply committed to addressing critical challenges such as environmental degradation, climate change, and mass youth unemployment,
              which threaten human security and the nation’s long-term stability. Additionally, {app.alt_name} engages in enlightenment campaigns, electoral education,
              and advocacy for the independence of the judiciary, all aimed at empowering citizens, ensuring fair and transparent elections, and strengthening the rule of law across Nigeria.
            </p>
          </div>
        </div>
      </div>




      <div class="col-sm-12 col-md-12 col-lg-6 bg-gray-100 text-gray-900 my-9">
        <div class="inner-padding">
          <div class="heading heading-2mb-30">
            <h2 class="heading__title">What does {app.alt_name} Believe?</h2>
            <p class="heading__desc">
              {app.alt_name} believes that creating inclusive governance structures is essential to overcoming the collective action failures that result in human rights abuses,
              corruption, and the erosion of the rule of law. We believe that Nigeria has the potential to advance by adopting development models that promote sustainable
              industrialization while preserving ecological integrity. We also believe that through our efforts—advocating for transparency, accountability, judicial
              independence, and civic empowerment—{app.alt_name} can play a vital role in achieving these objectives and fostering a just and prosperous society.
            </p>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4 pt-100 pb-100 my-9 rounded">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/banners/nigeria-elections.jpg') }" alt="background" />
        </div>
      </div>



    </div>
  </div>
</section>

<style lang="scss">
  #banner3{

    .heading__title{
      font-size: 1.8rem;
      margin-bottom: 5px;

      &:not(:first-child){
        margin-top: 20px;
      }
    }

    .heading__desc{
      font-size: 16px;
    }
  }
</style>

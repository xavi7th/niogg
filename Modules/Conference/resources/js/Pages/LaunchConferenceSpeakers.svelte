<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";

  $: ({ app } = $page.props);
</script>

<h2 class="text-4xl text-gray-800">Our Esteemed Speakers</h2>
<div class="projects-carousel-2 carousel owl-carousel carousel-arrows" data-slide="3" data-slide-md="2"
  data-slide-sm="1" data-autoplay="true" data-nav="true" data-dots="false" data-space="30" data-loop="true"
  data-speed="800">
  <div class="project-item">
    <div class="project__img">
      <enhanced:img src="@publicpage-assets/images/src/speakers/neil-mccluskey.jpg?enhanced&h=250&w=250fit=crop" class="w-full !rounded-3xl" alt="neil-mccluskey-thumb" />
    </div>
    <div class="project__content">
      <h4 class="project__title"><a href="#">Neil McCluskey</a></h4>
      <div class="project__cat">
        <a href="#">HR Professional</a><a href="#">United Kingdom</a>
      </div>
    </div>
  </div>
  <div class="project-item">
    <div class="project__img">
      <enhanced:img src="@publicpage-assets/images/src/speakers/sania-ansari.jpg?enhanced&h=250&w=250fit=crop" class="w-full !rounded-3xl" alt="sania-ansari-thumb" />
    </div>
    <div class="project__content">
      <h4 class="project__title"><a href="https://ae.linkedin.com/in/he-dr-sania-a-ansari" target="_blank">H.E. Dr. Sania A Ansari</a></h4>
      <div class="project__cat">
        <a href="#/">Chairperson</a><a href="https://ansarigroups.com/" target="_blank">Ansari Group LTD</a>
      </div>
    </div>
  </div>
  <div class="project-item">
    <div class="project__img">
      <enhanced:img src="@publicpage-assets/images/src/speakers/allen-onyema.jpg?enhanced&h=250&w=250fit=crop" class="w-full !rounded-3xl" alt="allen-onyema-thumb" />
    </div>
    <div class="project__content">
      <h4 class="project__title"><a href="https://en.wikipedia.org/wiki/Allen_Onyema" target="_blank">Barr. Allen I. Onyema</a></h4>
      <div class="project__cat">
        <a href="#/">CEO</a><a href="#">Air Peace</a>
      </div>
    </div>
  </div>
  <div class="project-item">
    <div class="project__img">
      <enhanced:img src="@publicpage-assets/images/src/speakers/hassan-imam-2.jpg?enhanced&h=250&w=250fit=crop" class="w-full !rounded-3xl" alt="hassan-imam-thumb" />
    </div>
    <div class="project__content">
      <h4 class="project__title"><a href="https://www.keystonebankng.com/staff-member/hassan-imam/" target="_blank">Hassan Imam</a></h4>
      <div class="project__cat">
        <a href="#/">Managing Director</a><a href="https://www.keystonebankng.com" target="_blank">Keystone Bank</a>
      </div>
    </div>
  </div>
  <div class="project-item">
    <div class="project__img">
      <enhanced:img src="@publicpage-assets/images/src/speakers/kafilat-araoye.jpg?enhanced&h=250&w=250fit=crop" class="w-full !rounded-3xl" alt="kafilat-araoye-thumb" />
    </div>
    <div class="project__content">
      <h4 class="project__title"><a href="https://www.lotusbank.com/team-details?a=Kafilat-Araoye" target="_blank">Mrs Kafiat Araroye</a></h4>
      <div class="project__cat">
        <a href="#/">Managing Director</a><a href="https://www.lotusbank.com/" target="_blank">LOTUS Bank</a>
      </div>
    </div>
  </div>
</div>

<style lang="scss">
  :global{
    .project-item{

      .project__img{

        &:after{
          border-radius: 1.5rem;
          background-image: none !important;
          border: 1px solid #8080803e;
        }
      }
    }
  }
</style>

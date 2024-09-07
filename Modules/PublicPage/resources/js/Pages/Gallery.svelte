<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Gallery'>
  <li class="breadcrumb-item active" aria-current="page">Gallery</li>
</PageTitle>

<section id="projectsGrid" class="projects projects-grid">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <ul class="projects-filter justify-content-center">
          <li><a class="filter active" href="#" data-filter="all">All</a></li>
          <li><a class="filter" href="#" data-filter=".filter-protests">Protests</a></li>
          <li><a class="filter" href="#" data-filter=".filter-awards">Awards</a></li>
          <li><a class="filter" href="#" data-filter=".filter-charity">Charity Drive</a></li>
          <li><a class="filter" href="#" data-filter=".filter-elections">Elections</a></li>
          <li><a class="filter" href="#" data-filter=".filter-medicals">Free Medicals</a></li>
        </ul>
      </div>
    </div>
    <div id="filtered-items-wrap" class="row">

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-awards">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/1.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Award for Whistle Blowing Initiatives</a></h4>
            <div class="project__cat">
              <a href="#">Awards</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-protests">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/2.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">#EndBadGovernmnent Protests 2024</a></h4>
            <div class="project__cat">
              <a href="#">Protests</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-charity">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/3.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Charity visit to Rewane Orphanage 2023</a></h4>
            <div class="project__cat">
              <a href="#">Charity Drive</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-elections">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/4.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Electoral Enlightenments</a></h4>
            <div class="project__cat">
              <a href="#">Elections</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-medicals">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/5.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Free Eye Testing for Otokutu and environs</a></h4>
            <div class="project__cat">
              <a href="#">Medicals</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-charity">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/6.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Charity visit to Rewane Orphanage 2023</a></h4>
            <div class="project__cat">
              <a href="#">Charity</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-elections project-hidden">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/1.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Charity visit to Rewane Orphanage 2023</a></h4>
            <div class="project__cat">
              <a href="#">Charity</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-protests project-hidden">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/3.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">Charity visit to Rewane Orphanage 2023</a></h4>
            <div class="project__cat">
              <a href="#">Charity Drive</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-md-6 col-lg-4 mix filter-medicals project-hidden">
        <div class="project-item">
          <div class="project__img">
            <enhanced:img src="@publicpage-assets/images/src/placeholders/2.jpg?enhanced" alt="gallery thumb"/>
          </div>
          <div class="project__content">
            <h4 class="project__title"><a href="#">#EndBadGovernmnent Protests 2024</a></h4>
            <div class="project__cat">
              <a href="#">Protests</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12 text-center">
        <a class="btn btn__primary btn__hover3 mt-20 loadMoreProjects" href="#">Load next</a>
      </div>
    </div>
  </div>
</section>

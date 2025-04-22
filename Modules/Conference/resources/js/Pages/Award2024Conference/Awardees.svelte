<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { Award } from 'lucide-svelte';
  import { page } from "@inertiajs/svelte";
  import AwardsList from './AwardsList.svelte';
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);
</script>

<PageTitle appName={app.name} pageTitle='Awardees and Recognitions'>
  <li class="breadcrumb-item active" aria-current="page">Awardees and Recognitions</li>
</PageTitle>

<section id="awards" class="awards pb-70 pt-0">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-10 offset-lg-1">
        <div class="heading heading-3 text-center mb-50">
          <div class="text-center mb-10">
            <div class="inline-block p-3 rounded-full bg-amber-100 text-amber-600 mb-4">
              <Award size={28} />
            </div>
            <span class="heading__subtitle !block">Good Governance takes effort</span>
            <h1 class="heading__title !text-3xl">
              Award Recipients
            </h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
              Honoring Sacrifice, Inspiring Change: Rewarding Those Who Drive Good Governance
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <AwardsList />
</section>

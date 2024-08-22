<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';
  import AboutTeam from '@publicpage-partials/About/AboutTeam.svelte';
  import OurPartners from '@publicpage-partials/HomePage/OurPartners.svelte';
  import ContactForm from '@publicpage-partials/HomePage/ContactForm.svelte';
  import SummarizedAbout from '@publicpage-partials/HomePage/SummarizedAbout.svelte';
  import NIOGGInitiatives from '@publicpage-partials/HomePage/NIOGGInitiatives.svelte';
  import AboutVissionMissionValues from '@publicpage-partials/About/AboutVissionMissionValues.svelte';

  $: ({ app } = $page.props);

  export let teams = [];
</script>

<PageTitle appName={app.name} pageTitle='About Us'>
  <li class="breadcrumb-item"><a href="#/">About Us</a></li>
  <li class="breadcrumb-item active" aria-current="page">Who We Are</li>
</PageTitle>

<SummarizedAbout appName={app.name} appAltName={app.alt_name}/>
<AboutVissionMissionValues appAltName={app.alt_name}/>
<AboutTeam {teams} appAltName={app.alt_name}/>
<NIOGGInitiatives appAltName={app.alt_name} />
<ContactForm appPhone={app.phone} appAltName={app.alt_name} />
<OurPartners />

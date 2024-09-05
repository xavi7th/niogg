<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { page } from "@inertiajs/svelte";
  import CTATwo from '@publicpage-partials/HomePage/CTATwo.svelte';
  import Features from '@publicpage-partials/HomePage/Features.svelte';
  import Slideshow from '@publicpage-partials/HomePage/Slideshow.svelte';
  import FundRaiser from '@publicpage-partials/HomePage/FundRaiser.svelte';
  import OurPartners from '@publicpage-partials/HomePage/OurPartners.svelte';
  import ContactForm from '@publicpage-partials/HomePage/ContactForm.svelte';
  import Testimonials from '@publicpage-partials/HomePage/Testimonials.svelte';
  import SummarizedAbout from '@publicpage-partials/HomePage/SummarizedAbout.svelte';
  import NIOGGInitiatives from '@publicpage-partials/HomePage/NIOGGInitiatives.svelte';
  import SummarizedWhatWeDo from '@publicpage-partials/HomePage/SummarizedWhatWeDo.svelte';
  import FeaturedBlogArticles from '@publicpage-partials/HomePage/FeaturedBlogArticles.svelte';

  /** @type {import('@root/types').Testimonial[]} */
  export let testimonials;

  $: ({ app } = $page.props);
</script>

<Slideshow appAltName={app.alt_name}/>
<Features appAltName={app.alt_name}/>
<SummarizedAbout appName={app.name} appAltName={app.alt_name}/>
<SummarizedWhatWeDo appAltName={app.alt_name}/>
<FundRaiser appAltName={app.alt_name}/>
<!-- DELETED <SummarizedProjects /> -->
<Testimonials {testimonials}/>
<NIOGGInitiatives appAltName={app.alt_name} />
<ContactForm appPhone={app.phone} appAltName={app.alt_name} />
<OurPartners />
<CTATwo appAltName={app.alt_name}/>
<FeaturedBlogArticles appAltName={app.alt_name}/>

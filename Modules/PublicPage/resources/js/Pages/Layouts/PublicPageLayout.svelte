<script>
	import { onMount } from "svelte";
  import { page } from '@inertiajs/svelte';
  import Header from '@publicpage-partials/Header.svelte';
  import Footer from '@publicpage-partials/Footer.svelte';
  import SearchModal from '@publicpage-partials/SearchModal.svelte';

  $: ({ app } = $page.props);

	let isMounted = false;

	export let pageTitle = 'NIOGG';

	onMount(() => {
		isMounted = true;
	});
</script>

<svelte:head>
	<title>{pageTitle} | NIOGG</title>
</svelte:head>

<main class="min-h-[60vh] wrapper">
  <Header appPhone={app.phone}/>

  <slot/>

  <Footer {app}/>
  <button id="scrollTopBtn"><i class="fa fa-long-arrow-up"></i></button>
  <SearchModal />
</main>

{#if isMounted}
	<script src="/build/assets/app-init.js"></script>
{/if}

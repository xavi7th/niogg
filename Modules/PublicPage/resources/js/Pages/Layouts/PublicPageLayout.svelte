<script>
	import { onMount } from "svelte";
  import { modalRoot } from "@/stores";
  import { page } from '@inertiajs/svelte';
  import { Portal } from "svelte-teleport";
  import Header from '@publicpage-partials/Header.svelte';
  import Footer from '@publicpage-partials/Footer.svelte';
  import SearchModal from '@publicpage-partials/SearchModal.svelte';

  $: ({ app } = $page.props);

	let isMounted = false;

	export let pageTitle = 'NIOGG';

	onMount(() => {
		isMounted = true;

    const style = document.createElement('style');
    style.innerHTML = `
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
    `;
    document.head.appendChild(style);

    return () => {
      document.head.removeChild(style);
    };
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

<Portal bind:this={$modalRoot} on:received={() => {}}></Portal>

<script>
  import { pageTitle, pageDescription } from "@/stores";
	import { onMount } from "svelte";
  import { Link } from '@inertiajs/svelte';
  import ApplicationLogo from '@/Components/ApplicationLogo.svelte';

	let isMounted = false;

	onMount(() => {
		isMounted = true;
		console.log("---====== UserAuth app mounted =====---");
	});
</script>

<svelte:head>
	<title>{ $pageTitle }</title>
	<meta name="description" content={ $pageDescription || $pageTitle } />
</svelte:head>

<template>
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
    <div>
      <Link href="/"><ApplicationLogo class="w-20 h-20 fill-current text-gray-500" /></Link>
    </div>

    <div
         class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
      <slot />
    </div>
  </div>
</template>

{#if isMounted}
	<script src="/build/assets/vendor/userauth-init.js"></script>
{/if}

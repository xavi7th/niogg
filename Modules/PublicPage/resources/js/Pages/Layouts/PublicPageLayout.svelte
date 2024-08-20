<script>
	import { onMount } from "svelte";
  import { Icons } from '@/Components/icons';
  import { Link, page } from "@inertiajs/svelte";

	let isMounted = false;

	export let pageTitle = 'PublicPage App', canLogin = false, canRegister = false, laravelVersion = undefined, phpVersion = undefined;

	onMount(() => {
		isMounted = true;
		console.log("---====== PublicPage app mounted =====---");
	});
</script>

<svelte:head>
	<title>{pageTitle} | NIOGG</title>
</svelte:head>

<div class="relative sm:flex sm:flex-col sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
  <div class="sm:fixed sm:top-0 sm:left-0 py-3 px-6 flex justify-center">
    {@html Icons.logo}
  </div>
  <div  class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
    <Link href="/" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</Link>

    {#if canLogin}
      {#if $page.props.auth.user}
        <Link href="{ window.route('appuser.dashboard') }" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</Link>
      {:else}
        <Link href="{ window.route('auth.login') }" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500" >Log in</Link>

        {#if canRegister}
          <Link v-if="canRegister" href="{ window.route('auth.register') }" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500" >Register</Link>
        {/if}
      {/if}
    {/if}
  </div>

  <main class="min-h-[60vh]">
    <slot/>
  </main>

  <footer class="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
    <div class="text-center text-sm sm:text-start">&nbsp;</div>

    <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-end sm:ms-0">
      Laravel {laravelVersion} (PHP v{phpVersion})
    </div>
  </footer>
</div>

{#if isMounted}
	<script src="/build/assets/vendor/publicpage-init.js"></script>
{/if}

<script>
	import { onMount } from "svelte";
	import { Link } from '@inertiajs/svelte';
	import { pageTitle, pageDescription, darkMode } from "@/stores";
	import ApplicationLogo from '@/Components/ApplicationLogo.svelte';
	import NotificationToast from '@/Components/NotificationToast.svelte';

	let isMounted = false;
	let mounted = false;

	onMount(() => {
		isMounted = true;
		mounted = true;
		console.log("---====== UserAuth app mounted =====---");
	});

	function toggleTheme() {
		darkMode.update((mode) => !mode);
	}
</script>

<svelte:head>
	<title>{ $pageTitle }</title>
	<meta name="description" content={ $pageDescription || $pageTitle } />
</svelte:head>

<template>
  <NotificationToast />

  <!-- Theme Toggle Button -->
  <button
    on:click={toggleTheme}
		class="fixed top-4 right-4 z-50 p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110
		{ $darkMode
			? 'bg-gray-800 text-yellow-400 hover:bg-gray-700'
			: 'bg-white text-gray-600 hover:bg-gray-50'
		}"
		aria-label="Toggle dark mode"
  >
	{#if $darkMode}
		<!-- Sun Icon -->
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
		</svg>
	{:else}
		<!-- Moon Icon -->
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
		</svg>
	{/if}
  </button>

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
    <!-- Logo with animation -->
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
      <Link href="/" class="flex flex-col items-center gap-3 group">
        <div class="relative">
          <img src="/build/img/niogg-logo.png" alt="NIOGG Logo" class="w-20 h-20 group-hover:scale-110 transition-transform duration-300" />
          <div class="absolute inset-0 bg-orange-400/20 rounded-full blur-xl group-hover:blur-2xl transition-all duration-300"></div>
        </div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">Welcome Back</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Sign in to access your dashboard</p>
      </Link>
    </div>

    <!-- Login Card with improved styling -->
    <div
		class="w-full sm:max-w-md mt-8 px-6 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl shadow-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-700"
		style="animation-delay: 100ms"
	>
      <slot />
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center animate-in fade-in slide-in-from-bottom-4 duration-700" style="animation-delay: 200ms">
      <p class="text-sm text-gray-500 dark:text-gray-400">
        © {new Date().getFullYear()} NIOGG. All rights reserved.
      </p>
    </div>
  </div>
</template>

{#if isMounted}
	<script src="/build/assets/userauth-init.js"></script>
{/if}

<script>
	import { page } from '@inertiajs/svelte';
	import { onMount } from 'svelte';

	export let currentPage = '';

	let isMobileMenuOpen = false;

	$: ({ url } = $page);

	// Detect current page from URL for active state
	$: activePage = (() => {
		const path = url?.pathname || '';
		if (path.includes('/admin/dashboard')) return 'dashboard';
		if (path.includes('/admin/events')) return 'events';
		if (path.includes('/admin/videos')) return 'videos';
		return 'dashboard';
	})();

	function toggleMobileMenu() {
		isMobileMenuOpen = !isMobileMenuOpen;
	}

	function closeMobileMenu() {
		isMobileMenuOpen = false;
	}

	const navItems = [
		{
			name: 'Dashboard',
			href: '/admin/dashboard',
			id: 'dashboard',
			icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'
		},
		{
			name: 'Events',
			href: '/admin/events',
			id: 'events',
			icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />'
		},
		{
			name: 'Logout',
			href: '/logout',
			id: 'logout',
			icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />',
			method: 'post'
		}
	];

	// Close mobile menu when route changes
	$: if ($page.url) {
		closeMobileMenu();
	}
</script>

<svelte:head>
	<title>Admin Sidebar</title>
</svelte:head>

<!-- Mobile Menu Button (visible on mobile only) -->
<button
	on:click={toggleMobileMenu}
	class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-[#1b1a1a] text-white rounded-lg hover:bg-[#333333] transition-colors"
	aria-label="Toggle menu"
>
	{#if isMobileMenuOpen}
		<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
		</svg>
	{:else}
		<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
		</svg>
	{/if}
</button>

<!-- Mobile Overlay -->
{#if isMobileMenuOpen}
	<div
		on:click={closeMobileMenu}
		class="lg:hidden fixed inset-0 bg-black/50 z-30 transition-opacity"
	></div>
{/if}

<!-- Sidebar -->
<aside
	class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-[#1b1a1a] text-white flex flex-col flex-shrink-0 transform transition-transform duration-300 ease-in-out
	{-isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}"
>
	<!-- Logo -->
	<div class="p-6 border-b border-[#333333]">
		<h1 class="text-xl font-bold text-[#ff7607]">NIOGG Admin</h1>
		<p class="text-xs text-[#9b9b9b] mt-1">Event & Video Management</p>
	</div>

	<!-- Navigation -->
	<nav class="flex-1 py-6 overflow-y-auto">
		{#each navItems as item}
			<a
				href={item.href}
				data-method={item.method || 'get'}
				on:click={closeMobileMenu}
				class="flex items-center gap-3 px-6 py-3 transition-colors
				{activePage === item.id && item.id !== 'logout'
					? 'bg-[#333333] text-[#ff7607] border-r-2 border-[#ff7607]'
					: 'text-[#9b9b9b] hover:bg-[#222222] hover:text-white'}"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					{@html item.icon}
				</svg>
				<span class="{activePage === item.id && item.id !== 'logout' ? 'font-medium' : ''}">{item.name}</span>
			</a>
		{/each}
	</nav>

	<!-- User Info -->
	<div class="p-6 border-t border-[#333333]">
		<div class="flex items-center gap-3">
			<div class="w-10 h-10 bg-[#ff7607] rounded-full flex items-center justify-center font-bold">
				{$page.props.auth?.user?.name?.charAt(0).toUpperCase() || 'A'}
			</div>
			<div class="flex-1 min-w-0">
				<p class="text-sm font-medium truncate">{$page.props.auth?.user?.name || 'Admin User'}</p>
				<p class="text-xs text-[#9b9b9b]">
					{$page.props.auth?.user?.is_super_admin ? 'Super Admin' : 'Admin'}
				</p>
			</div>
		</div>
	</div>
</aside>

<!-- Spacer for desktop layout -->
<div class="hidden lg:block w-64 flex-shrink-0"></div>

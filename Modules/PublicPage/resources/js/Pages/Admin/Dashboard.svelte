<script>
	import { page } from '@inertiajs/svelte';
	import { onMount } from 'svelte';
	import { tweened } from 'svelte/motion';
	import { cubicOut } from 'svelte/easing';
	import AdminSidebar from '../../Components/Admin/AdminSidebar.svelte';

	$: ({ stats, categoryStats, recentEvents, recentActivity, auth } = $page.props);

	// Animated counters
	let totalEvents = tweened(0, { duration: 1000, easing: cubicOut });
	let publishedEvents = tweened(0, { duration: 1000, easing: cubicOut });
	let totalVideos = tweened(0, { duration: 1000, easing: cubicOut });
	let draftEvents = tweened(0, { duration: 1000, easing: cubicOut });
	let featuredVideos = tweened(0, { duration: 1000, easing: cubicOut });
	let upcomingEvents = tweened(0, { duration: 1000, easing: cubicOut });
	let storageBytes = tweened(0, { duration: 1200, easing: cubicOut });
	let avgVideos = tweened(0, { duration: 1000, easing: cubicOut });

	let animated = false;

	onMount(() => {
		// Trigger animations after a small delay
		setTimeout(() => {
			totalEvents.set(stats?.total_events || 0);
			publishedEvents.set(stats?.published_events || 0);
			totalVideos.set(stats?.total_videos || 0);
			draftEvents.set(stats?.draft_events || 0);
			featuredVideos.set(stats?.featured_videos || 0);
			upcomingEvents.set(stats?.upcoming_events || 0);
			storageBytes.set(stats?.storage_used_bytes || 0);
			avgVideos.set(stats?.avg_videos_per_event || 0);
			animated = true;
		}, 100);
	});

	function formatDate(dateStr) {
		if (!dateStr) return 'N/A';
		return new Date(dateStr).toLocaleDateString('en-US', {
			month: 'short',
			day: 'numeric',
			year: 'numeric'
		});
	}

	function formatBytes(bytes) {
		if (!bytes || bytes === 0) return '0 B';
		const k = 1024;
		const sizes = ['B', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
	}

	function formatActivityIcon(type) {
		if (type === 'event_created') {
			return `M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z`;
		} else if (type === 'video_uploaded') {
			return `M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z`;
		}
		return `M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z`;
	}

	function getActivityColor(type) {
		return type === 'event_created' ? 'bg-gradient-to-br from-orange-100 to-orange-200 text-orange-600' : 'bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600';
	}
</script>

<svelte:head>
	<title>Dashboard | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-orange-50">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0 overflow-hidden">
		<!-- Animated Header -->
		<header
			class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 px-4 sm:px-6 lg:px-8 py-6 animate-in fade-in slide-in-from-top-4 duration-700"
		>
			<div class="flex items-center justify-between">
				<div>
					<div class="flex items-center gap-3">
						<div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
							<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
							</svg>
						</div>
						<div>
							<h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
								Dashboard
							</h1>
							<p class="text-sm text-gray-500 mt-0.5">Welcome back, <span class="font-medium text-orange-600">{auth?.user?.name || 'Admin'}</span></p>
						</div>
					</div>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-4 sm:p-6 lg:p-8 space-y-6 overflow-y-auto">
			<!-- Stats Cards Row 1 -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
				<!-- Total Events -->
				<div
					class="group relative bg-gradient-to-br from-white to-orange-50/50 rounded-2xl p-6 border border-orange-100 shadow-sm hover:shadow-xl hover:shadow-orange-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 0ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Total Events</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-orange-500 bg-clip-text text-transparent">{Math.round($totalEvents)}</p>
					</div>
				</div>

				<!-- Published Events -->
				<div
					class="group relative bg-gradient-to-br from-white to-emerald-50/50 rounded-2xl p-6 border border-emerald-100 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 50ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Published Events</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-500 bg-clip-text text-transparent">{Math.round($publishedEvents)}</p>
					</div>
				</div>

				<!-- Total Videos -->
				<div
					class="group relative bg-gradient-to-br from-white to-blue-50/50 rounded-2xl p-6 border border-blue-100 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 100ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Total Videos</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">{Math.round($totalVideos)}</p>
					</div>
				</div>

				<!-- Draft Events -->
				<div
					class="group relative bg-gradient-to-br from-white to-amber-50/50 rounded-2xl p-6 border border-amber-100 shadow-sm hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 150ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Draft Events</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-amber-600 to-amber-500 bg-clip-text text-transparent">{Math.round($draftEvents)}</p>
					</div>
				</div>
			</div>

			<!-- Stats Cards Row 2 -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
				<!-- Featured Videos -->
				<div
					class="group relative bg-gradient-to-br from-white to-pink-50/50 rounded-2xl p-6 border border-pink-100 shadow-sm hover:shadow-xl hover:shadow-pink-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 200ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-pink-400 to-pink-500 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
								</svg>
							</div>
							<div class="px-3 py-1.5 bg-pink-100 rounded-lg">
								<svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
									<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Featured Videos</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-pink-500 bg-clip-text text-transparent">{Math.round($featuredVideos)}</p>
					</div>
				</div>

				<!-- Upcoming Events -->
				<div
					class="group relative bg-gradient-to-br from-white to-indigo-50/50 rounded-2xl p-6 border border-indigo-100 shadow-sm hover:shadow-xl hover:shadow-indigo-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 250ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Upcoming Events</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-indigo-500 bg-clip-text text-transparent">{Math.round($upcomingEvents)}</p>
					</div>
				</div>

				<!-- Storage Used -->
				<div
					class="group relative bg-gradient-to-br from-white to-teal-50/50 rounded-2xl p-6 border border-teal-100 shadow-sm hover:shadow-xl hover:shadow-teal-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 300ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-teal-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-teal-400 to-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-teal-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Storage Used</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-teal-600 to-teal-500 bg-clip-text text-transparent">{formatBytes($storageBytes)}</p>
					</div>
				</div>

				<!-- Avg Videos Per Event -->
				<div
					class="group relative bg-gradient-to-br from-white to-purple-50/50 rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl hover:shadow-purple-500/10 hover:-translate-y-1 transition-all duration-300 animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 350ms"
				>
					<div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-400/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
					<div class="relative">
						<div class="flex items-center justify-between mb-4">
							<div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
								<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
								</svg>
							</div>
							<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
								<svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
								</svg>
							</div>
						</div>
						<p class="text-sm font-medium text-gray-500 mb-1">Avg Videos/Event</p>
						<p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-purple-500 bg-clip-text text-transparent">{$avgVideos.toFixed(1)}</p>
					</div>
				</div>
			</div>

			<!-- Two Column Layout -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
				<!-- Category Breakdown -->
				<div
					class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 p-6 shadow-sm animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 400ms"
				>
					<div class="flex items-center gap-3 mb-6">
						<div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
							<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
							</svg>
						</div>
						<div>
							<h3 class="text-lg font-bold text-gray-900">Events by Category</h3>
							<p class="text-sm text-gray-500">Distribution across categories</p>
						</div>
					</div>
					{#if categoryStats && categoryStats.length > 0}
						<div class="space-y-4">
							{#each categoryStats as category, idx}
								<div class="group">
									<div class="flex items-center justify-between mb-2">
										<div class="flex items-center gap-3">
											<div class="w-3 h-3 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full shadow-lg shadow-orange-500/30"></div>
											<span class="text-sm font-medium text-gray-700">{category.category}</span>
										</div>
										<span class="text-sm font-bold text-gray-900">{category.count}</span>
									</div>
									<div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
										<div
											class="h-2.5 rounded-full bg-gradient-to-r from-orange-400 to-orange-500 transition-all duration-1000 ease-out"
											style="width: 0%; animation: progress-fill 1s ease-out {idx * 100 + 500}ms forwards; --target-width: {(category.count / stats.total_events * 100) || 0}%"
										></div>
									</div>
								</div>
							{/each}
						</div>
					{:else}
						<div class="text-center py-8">
							<div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
								</svg>
							</div>
							<p class="text-sm text-gray-500">No categories yet.</p>
						</div>
					{/if}
				</div>

				<!-- Recent Activity -->
				<div
					class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 p-6 shadow-sm animate-in fade-in slide-in-from-bottom-4 duration-500"
					style="animation-delay: 450ms"
				>
					<div class="flex items-center gap-3 mb-6">
						<div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
							<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div>
							<h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
							<p class="text-sm text-gray-500">Latest updates and changes</p>
						</div>
					</div>
					{#if recentActivity && recentActivity.length > 0}
						<div class="space-y-3">
							{#each recentActivity as activity, idx}
								<div
									class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50/50 transition-all duration-200 group animate-in fade-in slide-in-from-left-2 duration-300"
									style="animation-delay: {idx * 50 + 500}ms"
								>
									<div class="w-10 h-10 {getActivityColor(activity.type)} rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={formatActivityIcon(activity.type)} />
										</svg>
									</div>
									<div class="flex-1 min-w-0">
										<p class="text-sm font-medium text-gray-800">{activity.message}</p>
										<p class="text-xs text-gray-500 mt-0.5">{activity.time}</p>
									</div>
								</div>
							{/each}
						</div>
					{:else}
						<div class="text-center py-8">
							<div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
								<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<p class="text-sm text-gray-500">No recent activity.</p>
						</div>
					{/if}
				</div>
			</div>

			<!-- Recent Events -->
			<div
				class="bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500"
				style="animation-delay: 500ms"
			>
				<!-- Header -->
				<div class="p-6 border-b border-gray-100">
					<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
								<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
								</svg>
							</div>
							<div>
								<h3 class="text-lg font-bold text-gray-900">Recent Events</h3>
								<p class="text-sm text-gray-500">Your latest events</p>
							</div>
						</div>
						<div class="flex gap-3">
							<a
								href="/admin/events"
								class="px-5 py-2.5 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 font-medium text-sm transition-all duration-200 flex items-center gap-2"
							>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
								</svg>
								View All
							</a>
							<a
								href="/admin/events/create"
								class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-xl hover:from-orange-600 hover:to-orange-700 font-medium text-sm flex items-center gap-2 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 transition-all duration-200"
							>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								New Event
							</a>
						</div>
					</div>
				</div>

				<!-- Table -->
				<div class="overflow-x-auto">
					<table class="w-full min-w-[600px]">
						<thead class="bg-gray-50/50">
							<tr>
								<th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Event Name</th>
								<th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
								<th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
								<th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
								<th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Videos</th>
								<th class="text-right py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100">
							{#if recentEvents && recentEvents.length > 0}
								{#each recentEvents as event}
									<tr class="hover:bg-gray-50/50 transition-colors duration-150">
										<td class="py-4 px-6">
											<div class="flex items-center gap-3">
												{#if event.icon}
													<div class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center text-white text-lg shadow-lg">
														{event.icon}
													</div>
												{/if}
												<span class="font-semibold text-gray-900">{event.name}</span>
											</div>
										</td>
										<td class="py-4 px-6">
											<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">{event.category || 'N/A'}</span>
										</td>
										<td class="py-4 px-6 text-sm text-gray-500">{formatDate(event.event_date)}</td>
										<td class="py-4 px-6">
											{#if event.is_published}
												<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-emerald-50 to-emerald-100 text-emerald-700 border border-emerald-200">
													<span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
													Published
												</span>
											{:else}
												<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-amber-50 to-amber-100 text-amber-700 border border-amber-200">Draft</span>
											{/if}
										</td>
										<td class="py-4 px-6">
											<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">{event.videos?.length || 0}</span>
										</td>
										<td class="py-4 px-6 text-right">
											<a
												href="/admin/events/{event.id}"
												class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-700 text-sm font-semibold transition-colors duration-150"
											>
												View
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
												</svg>
											</a>
										</td>
									</tr>
								{/each}
							{:else}
								<tr>
									<td colspan="6" class="py-12">
										<div class="text-center">
											<div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
												<svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
												</svg>
											</div>
											<p class="text-gray-500 font-medium mb-2">No events yet</p>
											<p class="text-sm text-gray-400">Create your first event to get started!</p>
										</div>
									</td>
								</tr>
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</main>
</div>

<style>
	@keyframes progress-fill {
		from {
			width: 0%;
		}
		to {
			width: var(--target-width);
		}
	}
</style>

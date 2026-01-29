<script>
	import { page } from '@inertiajs/svelte';
	import AdminSidebar from '../../Components/Admin/AdminSidebar.svelte';

	$: ({ stats, categoryStats, recentEvents, recentActivity, auth } = $page.props);

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
		return type === 'event_created' ? 'bg-[#fff3e6] text-[#ff7607]' : 'bg-[#dbeafe] text-[#3b82f6]';
	}
</script>

<svelte:head>
	<title>Dashboard | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-4 sm:px-6 lg:px-8 py-4">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-xl sm:text-2xl font-bold text-[#1b1a1a]">Dashboard</h1>
					<p class="text-sm text-[#9b9b9b]">Welcome back, {auth?.user?.name || 'Admin'}</p>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-4 sm:p-6 lg:p-8">
			<!-- Stats Cards Row 1 -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
				<!-- Total Events -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#fff3e6] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#ff7607]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Total Events</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.total_events || 0}</p>
				</div>

				<!-- Published Events -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#d1fae5] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Published Events</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.published_events || 0}</p>
				</div>

				<!-- Total Videos -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#dbeafe] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Total Videos</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.total_videos || 0}</p>
				</div>

				<!-- Draft Events -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#fef3c7] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#f59e0b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Draft Events</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.draft_events || 0}</p>
				</div>
			</div>

			<!-- Stats Cards Row 2 -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
				<!-- Featured Videos -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#fce7f3] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#ec4899]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Featured Videos</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.featured_videos || 0}</p>
				</div>

				<!-- Upcoming Events -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#e0e7ff] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#6366f1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Upcoming Events</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.upcoming_events || 0}</p>
				</div>

				<!-- Storage Used -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#d1fae5] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Storage Used</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{formatBytes(stats?.storage_used_bytes || 0)}</p>
				</div>

				<!-- Avg Videos Per Event -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#f3e8ff] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#9333ea]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Avg Videos/Event</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{(stats?.avg_videos_per_event || 0).toFixed(1)}</p>
				</div>
			</div>

			<!-- Two Column Layout -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
				<!-- Category Breakdown -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-6">
					<h3 class="text-lg font-semibold text-[#1b1a1a] mb-4">Events by Category</h3>
					{#if categoryStats && categoryStats.length > 0}
						<div class="space-y-3">
							{#each categoryStats as category}
								<div class="flex items-center justify-between">
									<div class="flex items-center gap-3">
										<div class="w-3 h-3 bg-[#ff7607] rounded-full"></div>
										<span class="text-sm text-[#1b1a1a]">{category.category}</span>
									</div>
									<span class="text-sm font-semibold text-[#1b1a1a]">{category.count}</span>
								</div>
								<div class="w-full bg-[#f9f9f9] rounded-full h-2">
									<div
										class="bg-[#ff7607] h-2 rounded-full"
										style="width: {(category.count / stats.total_events * 100) || 0}%"
									></div>
								</div>
							{/each}
						</div>
					{:else}
						<p class="text-sm text-[#9b9b9b]">No categories yet.</p>
					{/if}
				</div>

				<!-- Recent Activity -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-6">
					<h3 class="text-lg font-semibold text-[#1b1a1a] mb-4">Recent Activity</h3>
					{#if recentActivity && recentActivity.length > 0}
						<div class="space-y-4">
							{#each recentActivity as activity}
								<div class="flex items-start gap-3">
									<div class="w-8 h-8 {getActivityColor(activity.type)} rounded-lg flex items-center justify-center flex-shrink-0">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d={formatActivityIcon(activity.type)} />
										</svg>
									</div>
									<div class="flex-1 min-w-0">
										<p class="text-sm text-[#1b1a1a]">{activity.message}</p>
										<p class="text-xs text-[#9b9b9b]">{activity.time}</p>
									</div>
								</div>
							{/each}
						</div>
					{:else}
						<p class="text-sm text-[#9b9b9b]">No recent activity.</p>
					{/if}
				</div>
			</div>

			<!-- Quick Actions -->
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
				<h2 class="text-lg font-semibold text-[#1b1a1a]">Recent Events</h2>
				<div class="flex gap-3">
					<a
						href="/admin/events"
						class="px-4 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium text-sm"
					>
						View All Events
					</a>
					<a
						href="/admin/events/create"
						class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm flex items-center gap-2"
					>
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						New Event
					</a>
				</div>
			</div>

			<!-- Events Table -->
			<div class="bg-white rounded-lg border border-[#eaeaea] overflow-hidden">
				<div class="overflow-x-auto">
					<table class="w-full min-w-[600px]">
						<thead class="bg-[#f9f9f9]">
							<tr>
								<th class="text-left py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Event Name</th>
								<th class="text-left py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Category</th>
								<th class="text-left py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Date</th>
								<th class="text-left py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Status</th>
								<th class="text-left py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Videos</th>
								<th class="text-right py-4 px-6 text-sm font-semibold text-[#1b1a1a]">Actions</th>
							</tr>
						</thead>
						<tbody>
							{#if recentEvents && recentEvents.length > 0}
								{#each recentEvents as event}
									<tr class="border-t border-[#eaeaea] hover:bg-[#f9f9f9]">
										<td class="py-4 px-6">
											<div class="flex items-center gap-3">
												{#if event.icon}
													<div class="w-10 h-10 bg-[#1b1a1a] rounded flex items-center justify-center text-white text-lg">
														{event.icon}
													</div>
												{/if}
												<span class="font-medium text-[#1b1a1a]">{event.name}</span>
											</div>
										</td>
										<td class="py-4 px-6 text-[#9b9b9b]">{event.category || 'N/A'}</td>
										<td class="py-4 px-6 text-[#9b9b9b]">{formatDate(event.event_date)}</td>
										<td class="py-4 px-6">
											{#if event.is_published}
												<span class="px-2 py-1 bg-[#d1fae5] text-[#10b981] text-xs font-medium rounded">Published</span>
											{:else}
												<span class="px-2 py-1 bg-[#fef3c7] text-[#f59e0b] text-xs font-medium rounded">Draft</span>
											{/if}
										</td>
										<td class="py-4 px-6 text-[#9b9b9b]">{event.videos?.length || 0}</td>
										<td class="py-4 px-6 text-right">
											<a href="/admin/events/{event.id}" class="text-[#ff7607] hover:underline text-sm font-medium mr-3">View</a>
										</td>
									</tr>
								{/each}
							{:else}
								<tr>
									<td colspan="6" class="py-8 text-center text-[#9b9b9b]">
										<p>No events yet. Create your first event to get started!</p>
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

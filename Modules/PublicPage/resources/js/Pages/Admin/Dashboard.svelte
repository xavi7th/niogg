<script>
	import { page } from '@inertiajs/svelte';
	import AdminSidebar from '../../Components/Admin/AdminSidebar.svelte';

	$: ({ stats, recentEvents, auth } = $page.props);

	function formatDate(dateStr) {
		if (!dateStr) return 'N/A';
		return new Date(dateStr).toLocaleDateString('en-US', {
			month: 'short',
			day: 'numeric',
			year: 'numeric'
		});
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
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
				<!-- Total Events -->
				<div class="bg-white rounded-lg p-6 border border-[#eaeaea]">
					<div class="flex items-center justify-between mb-4">
						<div class="w-12 h-12 bg-[#fff3e6] rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-[#ff7607]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
								/>
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
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
								/>
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
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
								/>
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
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"
								/>
							</svg>
						</div>
					</div>
					<p class="text-sm text-[#9b9b9b]">Draft Events</p>
					<p class="text-3xl font-bold text-[#1b1a1a]">{stats?.draft_events || 0}</p>
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
											<span class="px-2 py-1 bg-[#d1fae5] text-[#10b981] text-xs font-medium rounded"
												>Published</span
											>
										{:else}
											<span class="px-2 py-1 bg-[#fef3c7] text-[#f59e0b] text-xs font-medium rounded"
												>Draft</span
											>
										{/if}
									</td>
									<td class="py-4 px-6 text-[#9b9b9b]">{event.videos?.length || 0}</td>
									<td class="py-4 px-6 text-right">
										<a
											href="/admin/events/{event.id}"
											class="text-[#ff7607] hover:underline text-sm font-medium mr-3"
											>View</a
										>
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

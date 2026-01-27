<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';

	$: ({ event, auth } = $page.props);

	let videoFilter = 'all'; // all, featured

	$: filteredVideos = event?.videos
		? event.videos.filter((v) => {
				if (videoFilter === 'featured') return v.is_featured;
				return true;
		  })
		: [];

	function formatDate(dateStr) {
		if (!dateStr) return 'N/A';
		return new Date(dateStr).toLocaleDateString('en-US', {
			month: 'short',
			day: 'numeric',
			year: 'numeric'
		});
	}

	function formatDuration(seconds) {
		if (!seconds) return '0:00';
		const mins = Math.floor(seconds / 60);
		const secs = seconds % 60;
		return `${mins}:${secs.toString().padStart(2, '0')}`;
	}

	function deleteVideo(videoId, videoTitle) {
		if (confirm(`Are you sure you want to delete "${videoTitle}"? This action cannot be undone.`)) {
			router.delete(`/admin/events/${event.id}/videos/${videoId}`, {
				onSuccess: () => {
					// Page will reload with updated data
				}
			});
		}
	}

	function getEventIcon(icon) {
		if (!icon) return null;
		if (/[\u{1F300}-\u{1F9FF}]/u.test(icon)) {
			return icon;
		}
		return null;
	}
</script>

<svelte:head>
	<title>{event?.name || 'Event Details'} | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<!-- Sidebar -->
	<aside class="w-64 bg-[#1b1a1a] text-white flex flex-col flex-shrink-0">
		<!-- Logo -->
		<div class="p-6 border-b border-[#333333]">
			<h1 class="text-xl font-bold text-[#ff7607]">NIOGG Admin</h1>
			<p class="text-xs text-[#9b9b9b] mt-1">Event & Video Management</p>
		</div>

		<!-- Navigation -->
		<nav class="flex-1 py-6">
			<a
				href="/admin/dashboard"
				class="flex items-center gap-3 px-6 py-3 text-[#9b9b9b] hover:bg-[#222222] hover:text-white transition-colors"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
					/>
				</svg>
				<span>Dashboard</span>
			</a>
			<a
				href="/admin/events"
				class="flex items-center gap-3 px-6 py-3 bg-[#333333] text-[#ff7607] border-r-2 border-[#ff7607]"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
					/>
				</svg>
				<span class="font-medium">Events</span>
			</a>
			<a
				href="/logout"
				class="flex items-center gap-3 px-6 py-3 text-[#9b9b9b] hover:bg-[#222222] hover:text-white transition-colors"
				method="post"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path
						stroke-linecap="round"
						stroke-linejoin="round"
						stroke-width="2"
						d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
					/>
				</svg>
				<span>Logout</span>
			</a>
		</nav>

		<!-- User -->
		<div class="p-6 border-t border-[#333333]">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 bg-[#ff7607] rounded-full flex items-center justify-center font-bold">
					{auth?.user?.name?.charAt(0).toUpperCase() || 'A'}
				</div>
				<div class="flex-1">
					<p class="text-sm font-medium">{auth?.user?.name || 'Admin User'}</p>
					<p class="text-xs text-[#9b9b9b]">{auth?.user?.is_super_admin ? 'Super Admin' : 'Admin'}</p>
				</div>
			</div>
		</div>
	</aside>

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-8 py-4">
			<div class="flex items-center justify-between">
				<div class="flex items-center gap-4">
					<a
						href="/admin/events"
						class="text-[#9b9b9b] hover:text-[#1b1a1a]"
					>
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M15 19l-7-7 7-7"
							/>
						</svg>
					</a>
					<div>
						<h1 class="text-2xl font-bold text-[#1b1a1a]">{event?.name || 'Event Details'}</h1>
						<p class="text-sm text-[#9b9b9b]">Manage videos for this event</p>
					</div>
				</div>
				<div class="flex items-center gap-3">
					<a
						href="/admin/events/{event?.id}/edit"
						class="px-4 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium text-sm"
					>
						Edit Event
					</a>
					<a
						href="/admin/events/{event?.id}/videos/create"
						class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm flex items-center gap-2"
					>
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						Add Video
					</a>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-8">
			{#if event}
				<!-- Event Info Card -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-6 mb-6">
					<div class="flex items-start gap-6">
						{#if getEventIcon(event.icon)}
							<div class="w-20 h-20 bg-[#1b1a1a] rounded-lg flex items-center justify-center text-4xl flex-shrink-0">
								{event.icon}
							</div>
						{:else}
							<div class="w-20 h-20 bg-[#1b1a1a] rounded-lg flex items-center justify-center flex-shrink-0">
								<svg class="w-10 h-10 text-[#9b9b9b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="1.5"
										d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
									/>
								</svg>
							</div>
						{/if}
						<div class="flex-1">
							<div class="flex items-start justify-between">
								<div>
									<h2 class="text-xl font-semibold text-[#1b1a1a]">{event.name}</h2>
									{#if event.description}
										<p class="text-[#9b9b9b] mt-1 max-w-2xl">{event.description}</p>
									{/if}
								</div>
								<div class="flex items-center gap-2">
									{#if event.is_published}
										<span class="px-2 py-1 bg-[#d1fae5] text-[#10b981] text-xs font-medium rounded"
											>Published</span
										>
									{:else}
										<span class="px-2 py-1 bg-[#fef3c7] text-[#f59e0b] text-xs font-medium rounded"
											>Draft</span
										>
									{/if}
									{#if event.category}
										<span class="px-2 py-1 bg-[#f9f9f9] text-[#9b9b9b] text-xs font-medium rounded"
											>{event.category}</span
										>
									{/if}
								</div>
							</div>
							<div class="flex items-center gap-6 mt-4 text-sm text-[#9b9b9b]">
								<span class="flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
										/>
									</svg>
									{formatDate(event.event_date)}
								</span>
								<span class="flex items-center gap-1">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
										/>
									</svg>
									{filteredVideos.length} video{filteredVideos.length !== 1 ? 's' : ''}
								</span>
								{#if event.slug}
									<span class="flex items-center gap-1">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"
											/>
										</svg>
										/events/{event.slug}
									</span>
								{/if}
							</div>
						</div>
					</div>
				</div>

				<!-- Videos Section -->
				<div class="flex items-center justify-between mb-4">
					<h2 class="text-lg font-semibold text-[#1b1a1a]">
						Videos ({filteredVideos.length})
					</h2>
					<div class="flex items-center gap-3">
						<select
							bind:value={videoFilter}
							class="px-3 py-2 border border-[#eaeaea] rounded text-sm focus:ring-2 focus:ring-[#ff7607] outline-none bg-white"
						>
							<option value="all">All Videos</option>
							<option value="featured">Featured Only</option>
						</select>
					</div>
				</div>

				{#if filteredVideos.length > 0}
					<!-- Videos Grid -->
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
						{#each filteredVideos as video}
							<div
								class="bg-white rounded-lg border border-[#eaeaea] overflow-hidden hover:shadow-lg transition-shadow"
							>
								<!-- Video Thumbnail -->
								<div class="relative h-36 bg-[#1b1a1a] flex items-center justify-center">
									{#if video.thumbnail_url}
										<img
											src={video.thumbnail_url}
											alt={video.title}
											class="w-full h-full object-cover"
										/>
									{:else}
										<svg
											class="w-12 h-12 text-white opacity-50"
											fill="currentColor"
											viewBox="0 0 24 24"
										>
											<path d="M8 5v14l11-7z" />
										</svg>
									{/if}
									{#if video.is_featured}
										<span
											class="absolute top-2 right-2 px-2 py-0.5 bg-[#ff7607] text-white text-xs font-medium rounded"
											>Featured</span
										>
									{/if}
									{#if video.duration_seconds}
										<span
											class="absolute bottom-2 right-2 px-2 py-0.5 bg-black bg-opacity-75 text-white text-xs rounded"
											>{formatDuration(video.duration_seconds)}
										</span>
									{/if}
								</div>

								<!-- Video Info -->
								<div class="p-3">
									<h3 class="font-medium text-[#1b1a1a] text-sm truncate">{video.title}</h3>
									{#if video.description}
										<p class="text-xs text-[#9b9b9b] mt-1 line-clamp-2">{video.description}</p>
									{/if}
									<div class="flex items-center justify-between mt-3">
										<span class="text-xs text-[#9b9b9b]">Order: {video.sort_order || 0}</span>
										<div class="flex gap-1">
											<a
												href="/admin/events/{event.id}/videos/{video.id}/edit"
												class="p-1 text-[#9b9b9b] hover:text-[#ff7607]"
												title="Edit video"
											>
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
													/>
												</svg>
											</a>
											<button
												on:click={() => deleteVideo(video.id, video.title)}
												class="p-1 text-[#9b9b9b] hover:text-[#ef4444]"
												title="Delete video"
											>
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
													/>
												</svg>
											</button>
										</div>
									</div>
								</div>
							</div>
						{/each}
					</div>
				{:else}
					<!-- Empty State -->
					<div class="bg-white rounded-lg border border-[#eaeaea] p-12 text-center">
						<svg
							class="w-16 h-16 text-[#9b9b9b] mx-auto mb-4"
							fill="none"
							stroke="currentColor"
							viewBox="0 0 24 24"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="1.5"
								d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
							/>
						</svg>
						<h3 class="text-lg font-medium text-[#1b1a1a] mb-2">No videos yet</h3>
						<p class="text-[#9b9b9b] mb-4">
							{videoFilter === 'featured'
								? 'No featured videos. Mark some videos as featured to see them here.'
								: 'Add videos to this event to get started.'}
						</p>
						{#if videoFilter !== 'featured'}
							<a
								href="/admin/events/{event.id}/videos/create"
								class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
							>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								Add First Video
							</a>
						{/if}
					</div>
				{/if}
			{:else}
				<!-- Event not found -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-12 text-center">
					<p class="text-[#9b9b9b]">Event not found.</p>
					<a href="/admin/events" class="text-[#ff7607] hover:underline">Back to Events</a>
				</div>
			{/if}
		</div>
	</main>
</div>

<style>
	:global(.line-clamp-1) {
		overflow: hidden;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 1;
	}

	:global(.line-clamp-2) {
		overflow: hidden;
		display: -webkit-box;
		-webkit-box-orient: vertical;
		-webkit-line-clamp: 2;
	}
</style>

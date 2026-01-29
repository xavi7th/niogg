<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';
	import AdminSidebar from '../../../Components/Admin/AdminSidebar.svelte';
	import VideoEditModal from '../../../Components/Admin/VideoEditModal.svelte';
	import DeleteConfirmationDialog from '../../../Components/Admin/DeleteConfirmationDialog.svelte';

	$: ({ event, auth } = $page.props);

	let videoFilter = 'all'; // all, featured
	let showEditModal = false;
	let selectedVideo = null;
	let showDeleteDialog = false;
	let videoToDelete = null;

	// Drag-drop state
	let draggedVideoId = null;
	let draggedOverVideoId = null;
	let isReordering = false;

	$: filteredVideos = event?.videos
		? event.videos.filter((v) => {
				if (videoFilter === 'featured') return v.is_featured;
				return true;
		  })
		: [];

	$: sortedVideos = [...filteredVideos].sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0));

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

	function confirmDeleteVideo() {
		if (!videoToDelete) return;

		router.delete(`/admin/events/${event.id}/videos/${videoToDelete.id}`, {
			onSuccess: () => {
				showDeleteDialog = false;
				videoToDelete = null;
			},
			onError: () => {
				showDeleteDialog = false;
				videoToDelete = null;
			}
		});
	}

	function openDeleteDialog(video) {
		videoToDelete = video;
		showDeleteDialog = true;
	}

	function closeDeleteDialog() {
		showDeleteDialog = false;
		videoToDelete = null;
	}

	function openEditModal(video) {
		selectedVideo = video;
		showEditModal = true;
	}

	function getEventIcon(icon) {
		if (!icon) return null;
		if (/[\u{1F300}-\u{1F9FF}]/u.test(icon)) {
			return icon;
		}
		return null;
	}

	// Drag-drop handlers
	function handleDragStart(videoId, event) {
		draggedVideoId = videoId;
		event.dataTransfer.effectAllowed = 'move';
		event.dataTransfer.setData('text/plain', videoId.toString());
		// Set drag image
		event.target.style.opacity = '0.5';
	}

	function handleDragEnd(event) {
		draggedVideoId = null;
		draggedOverVideoId = null;
		event.target.style.opacity = '1';
	}

	function handleDragOver(videoId, event) {
		event.preventDefault();
		event.dataTransfer.dropEffect = 'move';
		if (draggedVideoId !== videoId) {
			draggedOverVideoId = videoId;
		}
	}

	function handleDragLeave(videoId) {
		if (draggedOverVideoId === videoId) {
			draggedOverVideoId = null;
		}
	}

	async function handleDrop(videoId, event) {
		event.preventDefault();
		draggedOverVideoId = null;

		if (draggedVideoId === null || draggedVideoId === videoId) {
			return;
		}

		// Calculate new order
		const draggedIndex = sortedVideos.findIndex((v) => v.id === draggedVideoId);
		const dropIndex = sortedVideos.findIndex((v) => v.id === videoId);

		if (draggedIndex === -1 || dropIndex === -1) {
			return;
		}

		// Create new order array
		const newOrder = [...sortedVideos];
		const [removed] = newOrder.splice(draggedIndex, 1);
		newOrder.splice(dropIndex, 0, removed);

		const videoIds = newOrder.map((v) => v.id);

		// Send reorder request
		isReordering = true;
		try {
			const response = await fetch(`/admin/videos/events/${event.id}/reorder`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
				},
				body: JSON.stringify({ video_ids: videoIds })
			});

			if (response.ok) {
				const data = await response.json();
				// Update local videos array with new sort_order
				sortedVideos.forEach((video, index) => {
					video.sort_order = index;
				});
				// Reload page to show updated order from server
				router.reload({ only: ['event'] });
			} else {
				const data = await response.json();
				window.ToastLarge.fire({
					title: 'Error',
					html: data.message || 'Failed to reorder videos.',
					icon: 'error',
					timer: 5000
				});
			}
		} catch (error) {
			window.ToastLarge.fire({
				title: 'Error',
				html: 'Network error. Please check your connection and try again.',
				icon: 'error',
				timer: 5000
			});
		} finally {
			isReordering = false;
			draggedVideoId = null;
		}
	}
</script>

<svelte:head>
	<title>{event?.name || 'Event Details'} | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-4 sm:px-6 lg:px-8 py-4">
			<div class="flex flex-col gap-4">
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
						<h1 class="text-xl sm:text-2xl font-bold text-[#1b1a1a]">{event?.name || 'Event Details'}</h1>
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
						href="/admin/videos/upload/{event?.id}"
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
		<div class="p-4 sm:p-6 lg:p-8">
			{#if event}
				<!-- Event Info Card -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-4 sm:p-6 mb-6">
					<div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
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
						<div class="flex-1 w-full">
							<div class="flex flex-col sm:flex-row items-start justify-between gap-4">
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
							<div class="flex flex-wrap items-center gap-4 sm:gap-6 mt-4 text-sm text-[#9b9b9b]">
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
									{sortedVideos.length} video{sortedVideos.length !== 1 ? 's' : ''}
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
				<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
					<div class="w-full sm:w-auto">
						<h2 class="text-lg font-semibold text-[#1b1a1a]">
							Videos ({sortedVideos.length})
						</h2>
						{#if sortedVideos.length > 1}
							<p class="text-sm text-[#9b9b9b] mt-1">
								Drag videos to reorder them
							</p>
						{/if}
					</div>
					<div class="flex items-center gap-3 w-full sm:w-auto">
						<select
							bind:value={videoFilter}
							class="w-full sm:w-auto px-3 py-2 border border-[#eaeaea] rounded text-sm focus:ring-2 focus:ring-[#ff7607] outline-none bg-white"
						>
							<option value="all">All Videos</option>
							<option value="featured">Featured Only</option>
						</select>
					</div>
				</div>

				{#if sortedVideos.length > 0}
					<!-- Videos Grid with Drag-Drop -->
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
						{#each sortedVideos as video}
							<div
								draggable="true"
								on:dragstart={(e) => handleDragStart(video.id, e)}
								on:dragend={handleDragEnd}
								on:dragover={(e) => handleDragOver(video.id, e)}
								on:dragleave={() => handleDragLeave(video.id)}
								on:drop={(e) => handleDrop(video.id, e)}
								class="bg-white rounded-lg border overflow-hidden hover:shadow-lg transition-all relative group"
								class:border-orange-300={draggedOverVideoId === video.id}
								class:ring-2={draggedOverVideoId === video.id}
								class:ring-orange-400={draggedOverVideoId === video.id}
								class:border-[#eaeaea]={draggedOverVideoId !== video.id}
								class:opacity-50={isReordering}
							>
								<!-- Drag Handle Indicator -->
								<div class="absolute top-2 left-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity bg-white rounded-md p-1 shadow-sm">
									<svg class="w-4 h-4 text-[#9b9b9b]" fill="currentColor" viewBox="0 0 24 24">
										<path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm8-14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
									</svg>
								</div>

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
									<div class="flex items-start gap-2">
										<svg class="w-4 h-4 text-[#9b9b9b] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
											<path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm8-14a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm0 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm-2 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
										</svg>
										<div class="flex-1 min-w-0">
											<h3 class="font-medium text-[#1b1a1a] text-sm truncate">{video.title}</h3>
											{#if video.description}
												<p class="text-xs text-[#9b9b9b] mt-1 line-clamp-2">{video.description}</p>
											{/if}
										</div>
									</div>
									<div class="flex items-center justify-between mt-3">
										<span class="text-xs text-[#9b9b9b]">Order: {video.sort_order || 0}</span>
										<div class="flex gap-1">
											<button
												on:click={() => openEditModal(video)}
												class="p-1 text-[#9b9b9b] hover:text-[#ff7607]"
												title="Edit video"
												type="button"
											>
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
													/>
												</svg>
											</button>
											<button
												on:click={() => openDeleteDialog(video)}
												class="p-1 text-[#9b9b9b] hover:text-[#ef4444]"
												title="Delete video"
												type="button"
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
								href="/admin/videos/upload/{event.id}"
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

<VideoEditModal bind:open={showEditModal} video={selectedVideo} eventId={event?.id} />

<DeleteConfirmationDialog
	bind:open={showDeleteDialog}
	title="Delete Video"
	message="Are you sure you want to delete this video?"
	itemName={videoToDelete?.title || ''}
	itemType="video"
	showWarning={false}
	warningMessage=""
	on:confirm={confirmDeleteVideo}
	on:cancel={closeDeleteDialog}
/>

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

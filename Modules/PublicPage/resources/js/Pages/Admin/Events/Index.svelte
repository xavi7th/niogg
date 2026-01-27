<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';
	import DeleteConfirmationDialog from '../../../Components/Admin/DeleteConfirmationDialog.svelte';

	$: ({ events, auth } = $page.props);

	let showDeleteDialog = false;
	let eventToDelete = null;

	let searchQuery = '';
	let statusFilter = 'all';
	let categoryFilter = 'all';

	// Extract unique categories from events
	$: categories = events?.data
		? [...new Set(events.data.map((e) => e.category).filter(Boolean))]
		: [];

	// Filter events client-side
	$: filteredEvents = events?.data
		? events.data.filter((event) => {
				// Status filter
				if (statusFilter === 'published' && !event.is_published) return false;
				if (statusFilter === 'draft' && event.is_published) return false;

				// Category filter
				if (categoryFilter !== 'all' && event.category !== categoryFilter) return false;

				// Search filter
				if (
					searchQuery &&
					!event.name?.toLowerCase().includes(searchQuery.toLowerCase()) &&
					!event.description?.toLowerCase().includes(searchQuery.toLowerCase())
				) {
					return false;
				}

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

	function getEventIcon(icon) {
		if (!icon) return null;
		// If it's an emoji, return it
		if (/[\u{1F300}-\u{1F9FF}]/u.test(icon)) {
			return icon;
		}
		// Otherwise return null or a default icon
		return null;
	}

	function confirmDeleteEvent() {
		if (!eventToDelete) return;

		router.delete(`/admin/events/${eventToDelete.id}`, {
			onSuccess: () => {
				showDeleteDialog = false;
				eventToDelete = null;
			},
			onError: () => {
				showDeleteDialog = false;
				eventToDelete = null;
			}
		});
	}

	function openDeleteDialog(event) {
		eventToDelete = event;
		showDeleteDialog = true;
	}

	function closeDeleteDialog() {
		showDeleteDialog = false;
		eventToDelete = null;
	}
</script>

<svelte:head>
	<title>Events | NIOGG Admin</title>
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
				<div>
					<h1 class="text-2xl font-bold text-[#1b1a1a]">Events</h1>
					<p class="text-sm text-[#9b9b9b]">Manage your events and media content</p>
				</div>
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
		</header>

		<!-- Content -->
		<div class="p-8">
			<!-- Tabs -->
			<div class="border-b border-[#eaeaea] mb-6">
				<nav class="flex gap-6">
					<button
						class="py-3 border-b-2 font-medium text-sm transition-colors"
						class:border-[#ff7607]={statusFilter === 'all'}
						class:border-transparent={statusFilter !== 'all'}
						class:text-[#ff7607]={statusFilter === 'all'}
						class:text-[#9b9b9b]={statusFilter !== 'all'}
						class:hover:text-[#1b1a1a]={statusFilter !== 'all'}
						on:click={() => (statusFilter = 'all')}
					>
						All Events
					</button>
					<button
						class="py-3 border-b-2 font-medium text-sm transition-colors"
						class:border-[#ff7607]={statusFilter === 'published'}
						class:border-transparent={statusFilter !== 'published'}
						class:text-[#ff7607]={statusFilter === 'published'}
						class:text-[#9b9b9b]={statusFilter !== 'published'}
						class:hover:text-[#1b1a1a]={statusFilter !== 'published'}
						on:click={() => (statusFilter = 'published')}
					>
						Published
					</button>
					<button
						class="py-3 border-b-2 font-medium text-sm transition-colors"
						class:border-[#ff7607]={statusFilter === 'draft'}
						class:border-transparent={statusFilter !== 'draft'}
						class:text-[#ff7607]={statusFilter === 'draft'}
						class:text-[#9b9b9b]={statusFilter !== 'draft'}
						class:hover:text-[#1b1a1a]={statusFilter !== 'draft'}
						on:click={() => (statusFilter = 'draft')}
					>
						Drafts
					</button>
				</nav>
			</div>

			<!-- Search & Filter Bar -->
			<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
				<div class="flex items-center gap-4">
					<!-- Search -->
					<div class="relative">
						<input
							type="text"
							placeholder="Search events..."
							bind:value={searchQuery}
							class="pl-10 pr-4 py-2 border border-[#eaeaea] rounded-lg w-64 focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
						/>
						<svg
							class="w-5 h-5 text-[#9b9b9b] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
							fill="none"
							stroke="currentColor"
							viewBox="0 0 24 24"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
							/>
						</svg>
					</div>

					<!-- Category Filter -->
					{#if categories.length > 0}
						<select
							bind:value={categoryFilter}
							class="px-4 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none bg-white"
						>
							<option value="all">All Categories</option>
							{#each categories as category}
								<option value={category}>{category}</option>
							{/each}
						</select>
					{/if}
				</div>

				<div class="text-sm text-[#9b9b9b]">
					{filteredEvents.length} event{filteredEvents.length !== 1 ? 's' : ''}
				</div>
			</div>

			<!-- Events Grid -->
			{#if filteredEvents.length > 0}
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					{#each filteredEvents as event}
						<div
							class="bg-white rounded-lg border border-[#eaeaea] overflow-hidden hover:shadow-lg transition-shadow"
						>
							<!-- Event Icon/Thumbnail -->
							<div class="h-40 bg-gradient-to-br from-[#1b1a1a] to-[#333333] flex items-center justify-center">
								{#if getEventIcon(event.icon)}
									<span class="text-6xl">{event.icon}</span>
								{:else}
									<svg
										class="w-16 h-16 text-[#9b9b9b]"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="1.5"
											d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
										/>
									</svg>
								{/if}
							</div>

							<!-- Event Info -->
							<div class="p-4">
								<div class="flex items-start justify-between mb-2">
									<h3 class="font-semibold text-[#1b1a1a] line-clamp-1">{event.name}</h3>
									{#if event.is_published}
										<span class="px-2 py-0.5 bg-[#d1fae5] text-[#10b981] text-xs font-medium rounded flex-shrink-0 ml-2"
											>Published</span
										>
									{:else}
										<span class="px-2 py-0.5 bg-[#fef3c7] text-[#f59e0b] text-xs font-medium rounded flex-shrink-0 ml-2"
											>Draft</span
										>
									{/if}
								</div>

								{#if event.description}
									<p class="text-sm text-[#9b9b9b] mb-3 line-clamp-2">{event.description}</p>
								{/if}

								<div class="flex items-center justify-between text-sm">
									<span class="text-[#9b9b9b]">{event.videos?.length || 0} videos</span>
									<span class="text-[#9b9b9b]">{formatDate(event.event_date)}</span>
								</div>

								<!-- Actions -->
								<div class="flex gap-2 mt-4 pt-4 border-t border-[#f9f9f9]">
									<a
										href="/admin/events/{event.id}"
										class="flex-1 px-3 py-2 bg-[#ff7607] text-white rounded text-sm font-medium hover:bg-[#e56a00] text-center"
									>
										View
									</a>
									<a
										href="/admin/events/{event.id}/edit"
										class="px-3 py-2 border border-[#eaeaea] rounded text-sm text-[#9b9b9b] hover:text-[#1b1a1a] hover:bg-[#f9f9f9]"
									>
										Edit
									</a>
									<button
										on:click={() => openDeleteDialog(event)}
										class="px-3 py-2 border border-[#eaeaea] rounded text-sm text-[#9b9b9b] hover:text-[#ef4444] hover:bg-[#fee2e2]"
										title="Delete event"
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
					{/each}
				</div>

				<!-- Pagination -->
				{#if events?.links}
					<div class="flex items-center justify-center gap-1 mt-8">
						{#if events.prev_page_url}
							<a
								href={events.prev_page_url}
								class="px-3 py-2 text-[#9b9b9b] hover:text-[#1b1a1a] hover:bg-white rounded"
							>
								&laquo; Previous
							</a>
						{/if}

						{#each Array(events.last_page || 1) as _, i}
							{@const page = i + 1}
							{#if page === events.current_page}
								<span class="px-3 py-2 bg-[#ff7607] text-white rounded font-medium">{page}</span>
							{:else}
								<a
									href="?page={page}"
									class="px-3 py-2 text-[#9b9b9b] hover:text-[#1b1a1a] hover:bg-white rounded"
								>
									{page}
								</a>
							{/if}
						{/each}

						{#if events.next_page_url}
							<a
								href={events.next_page_url}
								class="px-3 py-2 text-[#9b9b9b] hover:text-[#1b1a1a] hover:bg-white rounded"
							>
								Next &raquo;
							</a>
						{/if}
					</div>
				{/if}
			{:else}
				<!-- Empty State -->
				<div class="text-center py-16">
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
							d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
						/>
					</svg>
					<h3 class="text-lg font-medium text-[#1b1a1a] mb-2">No events found</h3>
					<p class="text-[#9b9b9b] mb-4">
						{searchQuery || statusFilter !== 'all' || categoryFilter !== 'all'
							? 'Try adjusting your filters or search query.'
							: 'Get started by creating your first event.'}
					</p>
					<a
						href="/admin/events/create"
						class="inline-flex items-center gap-2 px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
					>
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						Create New Event
					</a>
				</div>
			{/if}
		</div>
	</main>
</div>

<DeleteConfirmationDialog
	bind:open={showDeleteDialog}
	title="Delete Event"
	message="Are you sure you want to delete this event? All associated videos will also be deleted."
	itemName={eventToDelete?.name || ''}
	itemType="event"
	showWarning={true}
	warningMessage="This will permanently delete the event and all its videos. This action cannot be undone."
	on:confirm={confirmDeleteEvent}
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

<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';
	import AdminSidebar from '../../../Components/Admin/AdminSidebar.svelte';

	$: ({ auth, errors, categories } = $page.props);

	let formData = {
		name: '',
		description: '',
		icon: '',
		category: '',
		event_date: '',
		is_published: false,
		slug: ''
	};

	let isLoading = false;
	let showIconPicker = false;

	// Common emoji categories
	const emojiCategories = {
		'Events & Celebrations': ['🎉', '🎊', '🎈', '🎁', '🏆', '🥇', '🎯', '🎪', '🎭'],
		'Business & Work': ['💼', '📊', '📈', '💡', '🏢', '📅', '✅', '📝', '📌', '🔔'],
		'Sports & Activities': ['⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🏊', '🎿', '🏋️', '🚴'],
		'Tech & Media': ['💻', '📱', '🎬', '📷', '🎥', '🎧', '🎙️', '💾', '📡', '🔌'],
		'Education': ['📚', '🎓', '✏️', '📐', '🔬', '💡', '🎨', '🎼', '🏛️', '🗺️'],
		'Food & Drink': ['🍕', '🍔', '🍰', '☕', '🍷', '🥂', '🍽️', '🍜', '🥘', '🍿'],
		'Travel': ['✈️', '🚗', '🚄', '🏨', '🏖️', '🗺️', '🏔', '🏝️', '🎒', '📸'],
		'Miscellaneous': ['⭐', '❤️', '🔥', '💎', '🚀', '🎯', '💪', '👏', '🙌', '🎊']
	};

	function handleSubmit() {
		isLoading = true;
		router.post('/admin/events', formData, {
			onSuccess: () => {
				isLoading = false;
			},
			onError: () => {
				isLoading = false;
			}
		});
	}

	function saveAsDraft() {
		formData.is_published = false;
		handleSubmit();
	}

	function selectIcon(emoji) {
		formData.icon = emoji;
		showIconPicker = false;
	}

	function generateSlugPreview(name) {
		if (!name) return '';
		return name
			.toLowerCase()
			.trim()
			.replace(/[^\w\s-]/g, '')
			.replace(/[\s_-]+/g, '-')
			.replace(/^-+|-+$/g, '');
	}

	$: slugPreview = generateSlugPreview(formData.name);

	function isValidSlug(slug) {
		// Slug must be: lowercase, alphanumeric, hyphens only, no consecutive hyphens
		// Must start and end with alphanumeric
		const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
		return slugRegex.test(slug) && slug.length >= 2 && slug.length <= 255;
	}

	$: slugError = formData.slug && !isValidSlug(formData.slug) ? 'Slug must contain only lowercase letters, numbers, and hyphens. No consecutive hyphens allowed.' : '';
</script>

<svelte:head>
	<title>Create Event | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-4 sm:px-6 lg:px-8 py-4">
			<div class="flex items-center gap-4">
				<a
					href="/admin/events"
					class="text-[#9b9b9b] hover:text-[#1b1a1a]"
				>
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
				<div>
					<h1 class="text-xl sm:text-2xl font-bold text-[#1b1a1a]">Create New Event</h1>
					<p class="text-sm text-[#9b9b9b]">Fill in the event details below</p>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-4 sm:p-6 lg:p-8">
			<form on:submit|preventDefault={handleSubmit} class="max-w-3xl mx-auto">
				<!-- Basic Info Section -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-4 sm:p-6 mb-6">
					<h2 class="text-lg font-semibold text-[#1b1a1a] mb-4">Basic Information</h2>

					<div class="space-y-4">
						<!-- Event Name -->
						<div>
							<label for="name" class="block text-sm font-medium text-[#1b1a1a] mb-1">
								Event Name *
							</label>
							<input
								type="text"
								id="name"
								bind:value={formData.name}
								placeholder="Enter event name"
								class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
								class:border-[#ef4444]={errors.name}
							/>
							<p class="text-xs text-[#9b9b9b] mt-1">
								A descriptive name for your event (e.g., "Annual Conference 2024")
							</p>
							{#if errors.name}
								<p class="text-sm text-[#ef4444] mt-1">{errors.name}</p>
							{/if}
						</div>

						<!-- Description -->
						<div>
							<label for="description" class="block text-sm font-medium text-[#1b1a1a] mb-1">
								Description
							</label>
							<textarea
								id="description"
								bind:value={formData.description}
								rows="4"
								placeholder="Enter event description"
								class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none resize-none"
								class:border-[#ef4444]={errors.description}
							></textarea>
							<p class="text-xs text-[#9b9b9b] mt-1">Provide a brief description of the event</p>
							{#if errors.description}
								<p class="text-sm text-[#ef4444] mt-1">{errors.description}</p>
							{/if}
						</div>

						<!-- Category & Date Row -->
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<!-- Category -->
							<div>
								<label for="category" class="block text-sm font-medium text-[#1b1a1a] mb-1">
									Category *
								</label>
								<select
									id="category"
									bind:value={formData.category}
									class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none bg-white"
									class:border-[#ef4444]={errors.category}
								>
									<option value="">Select category</option>
									{#each categories || [] as cat}
										<option value={cat}>{cat}</option>
									{/each}
								</select>
								{#if errors.category}
									<p class="text-sm text-[#ef4444] mt-1">{errors.category}</p>
								{/if}
							</div>

							<!-- Event Date -->
							<div>
								<label for="event_date" class="block text-sm font-medium text-[#1b1a1a] mb-1">
									Event Date *
								</label>
								<input
									type="date"
									id="event_date"
									bind:value={formData.event_date}
									class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
									class:border-[#ef4444]={errors.event_date}
								/>
								{#if errors.event_date}
									<p class="text-sm text-[#ef4444] mt-1">{errors.event_date}</p>
								{/if}
							</div>
						</div>

						<!-- Icon Selector -->
						<div>
							<label class="block text-sm font-medium text-[#1b1a1a] mb-1">
								Event Icon (Emoji)
							</label>
							<div class="space-y-3">
								<!-- Icon Display and Picker Button -->
								<div class="flex items-center gap-3">
									<div
										class="w-16 h-16 border-2 border-dashed border-[#eaeaea] rounded-xl flex items-center justify-center text-4xl cursor-pointer hover:border-[#ff7607] hover:bg-[#fff3e6] transition-colors"
										on:click={() => showIconPicker = !showIconPicker}
									>
										{#if formData.icon}
											{formData.icon}
										{:else}
											<span class="text-gray-300 text-2xl">+</span>
										{/if}
									</div>
									<div class="flex-1">
										<input
											type="text"
											id="icon"
											bind:value={formData.icon}
											placeholder="🎬"
											maxlength="2"
											class="w-24 px-3 py-2 border border-[#eaeaea] rounded text-center text-2xl focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
											class:border-[#ef4444]={errors.icon}
										/>
										<button
											type="button"
											on:click={() => showIconPicker = !showIconPicker}
											class="px-3 py-2 bg-[#f9f9f9] hover:bg-[#f0f0f0] border border-[#eaeaea] rounded text-sm font-medium transition-colors"
										>
											Choose Icon
										</button>
									</div>
								</div>
								<p class="text-sm text-[#9b9b9b]">Choose an emoji to represent your event</p>
								{#if errors.icon}
									<p class="text-sm text-[#ef4444] mt-1">{errors.icon}</p>
								{/if}

								<!-- Icon Picker Modal -->
								{#if showIconPicker}
									<div class="mt-4 p-4 bg-gray-50 rounded-xl border border-[#eaeaea]">
										<div class="flex justify-between items-center mb-3">
											<h3 class="text-sm font-semibold text-[#1b1a1a]">Select an Icon</h3>
											<button
												type="button"
												on:click={() => showIconPicker = false}
												class="text-gray-400 hover:text-gray-600"
											>
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
												</svg>
											</button>
										</div>
										<div class="space-y-4 max-h-64 overflow-y-auto">
											{#each Object.entries(emojiCategories) as [category, emojis]}
												<div>
													<p class="text-xs font-medium text-gray-500 mb-2">{category}</p>
													<div class="flex flex-wrap gap-1">
														{#each emojis as emoji}
															<button
																type="button"
																on:click={() => selectIcon(emoji)}
																class="w-10 h-10 text-2xl flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm transition-all border border-transparent hover:border-[#eaeaea]"
															>
																{emoji}
															</button>
														{/each}
													</div>
												</div>
											{/each}
										</div>
									</div>
								{/if}
							</div>
						</div>
					</div>
				</div>

				<!-- Publishing Section -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-4 sm:p-6 mb-6">
					<h2 class="text-lg font-semibold text-[#1b1a1a] mb-4">Publishing</h2>

					<!-- Publish Toggle -->
					<div class="flex items-center justify-between py-3 border-b border-[#f9f9f9]">
						<div>
							<p class="font-medium text-[#1b1a1a]">Publish Event</p>
							<p class="text-sm text-[#9b9b9b]">Make this event visible on the public site</p>
						</div>
						<label class="relative inline-flex items-center cursor-pointer">
							<input
								type="checkbox"
								bind:checked={formData.is_published}
								class="sr-only peer"
							/>
							<div
								class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-[#ff7607] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ff7607]"
							></div>
						</label>
					</div>

					<!-- Slug Preview -->
					<div class="py-3">
						<label for="slug" class="block text-sm font-medium text-[#1b1a1a] mb-1">URL Slug</label>
						<div class="flex items-center">
							<span class="text-[#9b9b9b] text-sm">/events/</span>
							<input
								type="text"
								id="slug"
								bind:value={formData.slug}
								class="flex-1 px-3 py-2 border rounded-l-none rounded-r focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none {errors.slug || slugError ? 'border-[#ef4444]' : ''}"
								placeholder="event-url-slug"
							/>
						</div>
						<div class="flex items-center justify-between mt-1">
							<p class="text-xs text-[#9b9b9b]">This will be the URL for your event page</p>
							<button
								type="button"
								on:click={() => formData.slug = generateSlugPreview(formData.name)}
								class="text-xs text-[#ff7607] hover:text-[#e56a00] font-medium"
							>
								Auto-generate from name
							</button>
						</div>
						{#if errors.slug}
							<p class="text-sm text-[#ef4444] mt-1">{errors.slug}</p>
						{:else if slugError}
							<p class="text-sm text-[#ef4444] mt-1">{slugError}</p>
						{/if}
					</div>
				</div>

				<!-- Actions -->
				<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
					<a
						href="/admin/events"
						class="px-6 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium"
					>
						Cancel
					</a>
					<button
						type="button"
						on:click={saveAsDraft}
						disabled={isLoading}
						class="px-6 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium disabled:opacity-50 disabled:cursor-not-allowed"
					>
						Save as Draft
					</button>
					<button
						type="submit"
						disabled={isLoading}
						class="px-6 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium disabled:opacity-50 disabled:cursor-not-allowed"
					>
						{isLoading ? 'Creating...' : 'Create Event'}
					</button>
				</div>
			</form>
		</div>
	</main>
</div>

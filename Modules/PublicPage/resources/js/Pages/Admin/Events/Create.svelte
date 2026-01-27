<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';
	import { useForm } from '@inertiajs/svelte';
	import AdminSidebar from '../../../Components/Admin/AdminSidebar.svelte';

	$: ({ auth, errors } = $page.props);

	const form = useForm({
		name: '',
		description: '',
		icon: '',
		category: '',
		event_date: '',
		is_published: false
	});

	function handleSubmit() {
		form.post('/admin/events', {
			onSuccess: () => {
				// Page will redirect on success
			}
		});
	}

	function saveAsDraft() {
		form.is_published = false;
		handleSubmit();
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

	$: slugPreview = generateSlugPreview(form.name);
</script>

<svelte:head>
	<title>Create Event | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-8 py-4">
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
					<h1 class="text-2xl font-bold text-[#1b1a1a]">Create New Event</h1>
					<p class="text-sm text-[#9b9b9b]">Fill in the event details below</p>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-8">
			<form on:submit|preventDefault={handleSubmit} class="max-w-3xl">
				<!-- Basic Info Section -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-6 mb-6">
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
								bind:value={form.name}
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
								bind:value={form.description}
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
						<div class="grid grid-cols-2 gap-4">
							<!-- Category -->
							<div>
								<label for="category" class="block text-sm font-medium text-[#1b1a1a] mb-1">
									Category *
								</label>
								<select
									id="category"
									bind:value={form.category}
									class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none bg-white"
									class:border-[#ef4444]={errors.category}
								>
									<option value="">Select category</option>
									<option>Conference</option>
									<option>Workshop</option>
									<option>Entertainment</option>
									<option>Sports</option>
									<option>Education</option>
									<option>Other</option>
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
									bind:value={form.event_date}
									class="w-full px-3 py-2 border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
									class:border-[#ef4444]={errors.event_date}
								/>
								{#if errors.event_date}
									<p class="text-sm text-[#ef4444] mt-1">{errors.event_date}</p>
								{/if}
							</div>
						</div>

						<!-- Icon/Emoji -->
						<div>
							<label for="icon" class="block text-sm font-medium text-[#1b1a1a] mb-1">
								Event Icon (Emoji)
							</label>
							<div class="flex items-center gap-3">
								<input
									type="text"
									id="icon"
									bind:value={form.icon}
									placeholder="🎬"
									maxlength="2"
									class="w-20 px-3 py-2 border border-[#eaeaea] rounded text-center text-2xl focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none"
									class:border-[#ef4444]={errors.icon}
								/>
								<span class="text-sm text-[#9b9b9b]">Choose an emoji to represent your event</span>
							</div>
							{#if errors.icon}
								<p class="text-sm text-[#ef4444] mt-1">{errors.icon}</p>
							{/if}
						</div>
					</div>
				</div>

				<!-- Publishing Section -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-6 mb-6">
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
								bind:checked={form.is_published}
								class="sr-only peer"
							/>
							<div
								class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-[#ff7607] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ff7607]"
							></div>
						</label>
					</div>

					<!-- Slug Preview -->
					<div class="py-3">
						<label class="block text-sm font-medium text-[#1b1a1a] mb-1">URL Slug</label>
						<div class="flex items-center">
							<span class="text-[#9b9b9b] text-sm">/events/</span>
							<input
								type="text"
								value={slugPreview}
								readonly
								class="flex-1 px-3 py-2 border border-[#eaeaea] rounded-l-none rounded-r focus:ring-2 focus:ring-[#ff7607] focus:border-transparent outline-none bg-[#f9f9f9]"
							/>
						</div>
						<p class="text-xs text-[#9b9b9b] mt-1">This will be the URL for your event page</p>
					</div>
				</div>

				<!-- Actions -->
				<div class="flex items-center justify-end gap-3">
					<a
						href="/admin/events"
						class="px-6 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium"
					>
						Cancel
					</a>
					<button
						type="button"
						on:click={saveAsDraft}
						disabled={form.processing}
						class="px-6 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium disabled:opacity-50 disabled:cursor-not-allowed"
					>
						Save as Draft
					</button>
					<button
						type="submit"
						disabled={form.processing}
						class="px-6 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium disabled:opacity-50 disabled:cursor-not-allowed"
					>
						{form.processing ? 'Creating...' : 'Create Event'}
					</button>
				</div>
			</form>
		</div>
	</main>
</div>

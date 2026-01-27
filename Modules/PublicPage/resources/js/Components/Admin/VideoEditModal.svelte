<script>
	import { fly } from 'svelte/transition';
	import { router } from '@inertiajs/svelte';
	import { useForm } from '@inertiajs/svelte';

	export let open = false;
	export let video = null;
	export let eventId = null;

	let form;

	$: if (video) {
		form = useForm({
			title: video.title || '',
			description: video.description || '',
			duration_seconds: video.duration_seconds || 0,
			is_featured: video.is_featured || false,
			sort_order: video.sort_order || 0,
		});
	}

	function close() {
		open = false;
	}

	function submit() {
		form.put(`/admin/videos/${video.id}`, {
			onSuccess: () => {
				close();
			},
		});
	}

	function onBackdropClick(e) {
		if (e.target === e.currentTarget) {
			close();
		}
	}
</script>

{#if open && video}
	<div
		role="presentation"
		class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
		on:click={onBackdropClick}
		transition:fly={{ y: 50, duration: 200 }}
	>
		<div class="bg-white rounded-lg shadow-xl w-full max-w-md">
			<!-- Modal Header -->
			<div class="flex items-center justify-between p-6 border-b border-[#eaeaea]">
				<h2 class="text-lg font-semibold text-[#1b1a1a]">Edit Video</h2>
				<button
					on:click={close}
					class="text-[#9b9b9b] hover:text-[#1b1a1a] transition-colors"
					type="button"
				>
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
						/>
					</svg>
				</button>
			</div>

			<!-- Modal Body -->
			<form on:submit|preventDefault={submit}>
				<div class="p-6 space-y-4">
					<!-- Title -->
					<div>
						<label for="title" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Title <span class="text-[#ef4444]">*</span>
						</label>
						<input
							id="title"
							type="text"
							bind:value={form.title}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none {form.errors.title
								? 'border-[#ef4444]'
								: ''}"
							placeholder="Video title"
						/>
						{#if form.errors.title}
							<p class="mt-1 text-sm text-[#ef4444]">{form.errors.title}</p>
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
							rows="3"
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none resize-none {form.errors.description
								? 'border-[#ef4444]'
								: ''}"
							placeholder="Video description (optional)"
						></textarea>
						{#if form.errors.description}
							<p class="mt-1 text-sm text-[#ef4444]">{form.errors.description}</p>
						{/if}
					</div>

					<!-- Duration -->
					<div>
						<label for="duration" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Duration (seconds)
						</label>
						<input
							id="duration"
							type="number"
							min="0"
							bind:value={form.duration_seconds}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none {form.errors.duration_seconds
								? 'border-[#ef4444]'
								: ''}"
							placeholder="0"
						/>
						{#if form.errors.duration_seconds}
							<p class="mt-1 text-sm text-[#ef4444]">{form.errors.duration_seconds}</p>
						{/if}
					</div>

					<!-- Sort Order -->
					<div>
						<label for="sort_order" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Sort Order
						</label>
						<input
							id="sort_order"
							type="number"
							min="0"
							bind:value={form.sort_order}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none {form.errors.sort_order
								? 'border-[#ef4444]'
								: ''}"
							placeholder="0"
						/>
						{#if form.errors.sort_order}
							<p class="mt-1 text-sm text-[#ef4444]">{form.errors.sort_order}</p>
						{/if}
					</div>

					<!-- Featured Toggle -->
					<div class="flex items-center justify-between">
						<div>
							<label for="featured" class="block text-sm font-medium text-[#1b1a1a]">
								Featured Video
							</label>
							<p class="text-xs text-[#9b9b9b]">Featured videos are highlighted on the event page</p>
						</div>
						<label class="relative inline-flex items-center cursor-pointer">
							<input
								id="featured"
								type="checkbox"
								bind:checked={form.is_featured}
								class="sr-only peer"
							/>
							<div
								class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff7607]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ff7607]"
							></div>
						</label>
					</div>
				</div>

				<!-- Modal Footer -->
				<div class="flex items-center justify-end gap-3 p-6 border-t border-[#eaeaea]">
					<button
						on:click={close}
						type="button"
						class="px-4 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium text-sm"
						disabled={form.processing}
					>
						Cancel
					</button>
					<button
						type="submit"
						class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
						disabled={form.processing}
					>
						{#if form.processing}
							Saving...
						{:else}
							Save Changes
						{/if}
					</button>
				</div>
			</form>
		</div>
	</div>
{/if}

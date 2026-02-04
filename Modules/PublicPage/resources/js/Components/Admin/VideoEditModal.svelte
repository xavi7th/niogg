<script>
  import { fly } from 'svelte/transition';
	import { router } from '@inertiajs/svelte';

	export let open = false;
	export let video = {};

	let isSubmitting = false;

	// Thumbnail upload state
	let thumbnailFile = null;
	let thumbnailPreview = null;
	let uploadingThumbnail = false;
	let thumbnailProgress = 0;
	let thumbnailError = null;
	let fileInput;

	// Current thumbnail URL (custom > auto > placeholder)
	$: currentThumbnail = video.custom_thumbnail_url || video.thumbnail_url || '/images/video-placeholder-default.jpg';

	function close() {
		open = false;
		isSubmitting = false;
	}

	function submit() {
		isSubmitting = true;
		router.put(`/admin/videos/${video.id}`, video, {
			onSuccess: () => {
				close();
			},
			onFinish: () => {
				isSubmitting = false;
			},
		});
	}

	function uploadThumbnail() {
		if (!thumbnailFile) {
			thumbnailError = 'Please select a file to upload';
			return;
		}

		uploadingThumbnail = true;
		thumbnailError = null;
		thumbnailProgress = 0;

		const formData = new FormData();
		formData.append('thumbnail', thumbnailFile);

		router.post(`/admin/videos/${video.id}/thumbnail`, formData, {
			forceFormData: true,
			onProgress: (progress) => {
				thumbnailProgress = Math.round(progress.detail.progress);
			},
			onSuccess: () => {
				// Reload to get updated video data with new thumbnail
				router.reload({
					onSuccess: () => {
						// Reset state after successful reload
						uploadingThumbnail = false;
						thumbnailFile = null;
						thumbnailPreview = null;
						thumbnailProgress = 0;
					},
				});
			},
			onError: (errors) => {
				uploadingThumbnail = false;
				thumbnailError = errors.thumbnail || 'Failed to upload thumbnail';
				thumbnailProgress = 0;
			},
		});
	}

	function onBackdropClick(e) {
		if (e.target === e.currentTarget) {
			close();
		}
	}

	function handleFileSelect(e) {
		const file = e.target.files[0];
		if (!file) {
			thumbnailFile = null;
			thumbnailPreview = null;
			thumbnailError = null;
			return;
		}

		// Validate file type
		if (!file.type.startsWith('image/')) {
			thumbnailError = 'Please select an image file';
			thumbnailFile = null;
			thumbnailPreview = null;
			return;
		}

		// Validate file size (5MB max)
		const maxSize = 5 * 1024 * 1024;
		if (file.size > maxSize) {
			thumbnailError = 'File size must be less than 5MB';
			thumbnailFile = null;
			thumbnailPreview = null;
			return;
		}

		thumbnailFile = file;
		thumbnailError = null;

		// Create preview URL
		thumbnailPreview = URL.createObjectURL(file);
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
							bind:value={video.title}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none"
							placeholder="Video title"
						/>
					</div>

					<!-- Custom Thumbnail -->
					<div>
						<label class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Custom Thumbnail
						</label>

						<!-- Current/Preview Thumbnail -->
						<div class="mb-3">
							<img
								src={thumbnailPreview || currentThumbnail}
								alt="Video thumbnail"
								class="max-h-48 w-full object-cover rounded-lg border-2 {video.custom_thumbnail_url
									? 'border-[#ff7607]'
									: 'border-[#eaeaea]'}"
							/>
							{#if video.custom_thumbnail_url}
								<p class="text-xs text-[#9b9b9b] mt-1">Custom thumbnail active</p>
							{/if}
						</div>

						<!-- Upload Controls -->
						<input
							type="file"
							bind:this={fileInput}
							on:change={handleFileSelect}
							accept="image/*"
							class="hidden"
						/>

						{#if uploadingThumbnail}
							<!-- Loading State -->
							<div class="flex items-center justify-center gap-2 py-3 border border-[#eaeaea] rounded-lg">
								<svg
									class="animate-spin h-5 w-5 text-[#ff7607]"
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 24 24"
								>
									<circle
										class="opacity-25"
										cx="12"
										cy="12"
										r="10"
										stroke="currentColor"
										stroke-width="4"
									></circle>
									<path
										class="opacity-75"
										fill="currentColor"
										d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
									></path>
								</svg>
								<span class="text-sm text-[#1b1a1a]">Uploading {thumbnailProgress}%</span>
							</div>
						{:else}
							{#if thumbnailFile}
								<!-- File Selected State -->
								<div class="space-y-2">
									<div class="flex items-center justify-between p-3 bg-[#f9f9f9] border border-[#eaeaea] rounded-lg">
										<div class="flex-1 min-w-0">
											<p class="text-sm font-medium text-[#1b1a1a] truncate">{thumbnailFile.name}</p>
											<p class="text-xs text-[#9b9b9b]">
												{(thumbnailFile.size / 1024 / 1024).toFixed(2)} MB
											</p>
										</div>
										<button
											type="button"
											on:click={() => {
												thumbnailFile = null;
												thumbnailPreview = null;
												thumbnailError = null;
												if (fileInput) fileInput.value = '';
											}}
											class="ml-3 text-[#9b9b9b] hover:text-[#ef4444] transition-colors"
										>
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M6 18L18 6M6 6l12 12"
												/>
											</svg>
										</button>
									</div>
									<button
										type="button"
										on:click={uploadThumbnail}
										class="w-full px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm"
									>
										Upload Thumbnail
									</button>
								</div>
							{:else}
								<!-- Default State -->
								<button
									type="button"
									on:click={() => fileInput?.click()}
									class="w-full px-4 py-2 border-2 border-dashed border-[#eaeaea] rounded-lg hover:border-[#ff7607] hover:bg-[#fff9f5] transition-colors"
								>
									<div class="flex flex-col items-center gap-1">
										<svg class="w-8 h-8 text-[#9b9b9b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
											/>
										</svg>
										<span class="text-sm font-medium text-[#1b1a1a]">Choose Image</span>
										<span class="text-xs text-[#9b9b9b]">JPG, PNG, WebP up to 5MB</span>
									</div>
								</button>
							{/if}
						{/if}

						<!-- Error Display -->
						{#if thumbnailError}
							<p class="text-sm text-[#ef4444] mt-1">{thumbnailError}</p>
						{/if}
					</div>

					<div>
						<label for="description" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Description
						</label>
						<textarea
							id="description"
							bind:value={video.description}
							rows="3"
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none resize-none"
							placeholder="Video description (optional)"
						></textarea>
					</div>

					<div>
						<label for="duration" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Duration (seconds)
						</label>
						<input
							id="duration"
							type="number"
							min="0"
							bind:value={video.duration_seconds}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none"
							placeholder="0"
						/>
					</div>

					<div>
						<label for="sort_order" class="block text-sm font-medium text-[#1b1a1a] mb-1">
							Sort Order
						</label>
						<input
							id="sort_order"
							type="number"
							min="0"
							bind:value={video.sort_order}
							class="w-full px-3 py-2 border border-[#eaeaea] rounded-lg focus:ring-2 focus:ring-[#ff7607] outline-none"
							placeholder="0"
						/>
					</div>

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
								bind:checked={video.is_featured}
								class="sr-only peer"
							/>
							<div
								class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ff7607]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ff7607]"
							></div>
						</label>
					</div>
				</div>

				<div class="flex items-center justify-end gap-3 p-6 border-t border-[#eaeaea]">
					<button
						on:click={close}
						type="button"
						class="px-4 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium text-sm"
						disabled={isSubmitting}
					>
						Cancel
					</button>
					<button
						type="submit"
						class="px-4 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
						disabled={isSubmitting}
					>
						{#if isSubmitting}
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

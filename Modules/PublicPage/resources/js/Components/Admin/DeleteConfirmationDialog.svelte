<script>
	import { createEventDispatcher } from 'svelte';
	import { fly } from 'svelte/transition';

	export let open = false;
	export let title = 'Confirm Delete';
	export let message = 'Are you sure you want to delete this item?';
	export let itemType = 'item';
	export let itemName = '';
	export let showWarning = false;
	export let warningMessage = '';
	export let isLoading = false;

	const dispatch = createEventDispatcher();

	function handleConfirm() {
		dispatch('confirm');
	}

	function handleCancel() {
		if (!isLoading) {
			open = false;
			dispatch('cancel');
		}
	}

	function handleBackdropClick(event) {
		if (event.target === event.currentTarget && !isLoading) {
			handleCancel();
		}
	}

	function handleKeydown(event) {
		if (event.key === 'Escape' && !isLoading) {
			handleCancel();
		}
	}
</script>

<svelte:window on:keydown={handleKeydown} />

{#if open}
	<div
		role="dialog"
		aria-modal="true"
		aria-labelledby="dialog-title"
		class="fixed inset-0 z-50 flex items-center justify-center p-4"
		on:click={handleBackdropClick}
	>
		<!-- Backdrop -->
		<div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" />

		<!-- Modal -->
		<div
			class="relative bg-white rounded-lg shadow-xl max-w-md w-full transition:fly={{ y: 20, duration: 200 }}"
		>
			<!-- Header -->
			<div class="flex items-center gap-3 p-6 border-b border-gray-200">
				<div
					class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0"
				>
					<svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path
							stroke-linecap="round"
							stroke-linejoin="round"
							stroke-width="2"
							d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
						/>
					</svg>
				</div>
				<h3 id="dialog-title" class="text-lg font-semibold text-gray-900">{title}</h3>
			</div>

			<!-- Body -->
			<div class="p-6">
				<p class="text-gray-700 mb-2">{message}</p>

				{#if itemName}
					<p class="font-medium text-gray-900 mb-4">"{itemName}"</p>
				{/if}

				{#if showWarning && warningMessage}
					<div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
						<div class="flex items-start gap-2">
							<svg
								class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5"
								fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24"
							>
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
								/>
							</svg>
							<p class="text-sm text-yellow-800">{warningMessage}</p>
						</div>
					</div>
				{/if}

				<p class="text-sm text-gray-500 mt-4">This action cannot be undone.</p>
			</div>

			<!-- Footer -->
			<div class="flex gap-3 justify-end p-6 border-t border-gray-200">
				<button
					on:click={handleCancel}
					disabled={isLoading}
					type="button"
					class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
				>
					Cancel
				</button>
				<button
					on:click={handleConfirm}
					disabled={isLoading}
					type="button"
					class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
				>
					{#if isLoading}
						<svg
							class="animate-spin h-4 w-4"
							fill="none"
							viewBox="0 0 24 24"
							aria-hidden="true"
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
					{/if}
					Delete {itemType}
				</button>
			</div>
		</div>
	</div>
{/if}

<script>
	import { page } from '@inertiajs/svelte';
	import { router } from '@inertiajs/svelte';
	import { onMount } from 'svelte';
	import { writable } from 'svelte/store';
	import AdminSidebar from '../../../Components/Admin/AdminSidebar.svelte';

	$: ({ event, auth } = $page.props);

	// Upload queue state - use Svelte store for better reactivity
	const uploadQueue = writable([]);
	let dropZoneActive = false;
	let fileInputElement;

	// Set up event listener when component mounts
	onMount(() => {
		console.log('onMount called, fileInputElement:', fileInputElement);
		if (fileInputElement) {
			console.log('Adding event listener to fileInputElement');
			fileInputElement.addEventListener('change', handleFileSelect);
		}
	});

	// Constants
	const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
	const MAX_FILE_SIZE = 1024 * 1024 * 1024; // 1GB
	const ALLOWED_TYPES = ['video/mp4', 'video/webm', 'video/quicktime'];

	// Handle drag and drop
	function handleDragOver(e) {
		e.preventDefault();
		dropZoneActive = true;
	}

	function handleDragLeave(e) {
		e.preventDefault();
		dropZoneActive = false;
	}

	function handleDrop(e) {
		e.preventDefault();
		dropZoneActive = false;

		const files = Array.from(e.dataTransfer.files);
		addFilesToQueue(files);
	}

	// Handle file input
	function handleFileSelect(e) {
		console.log('handleFileSelect called!', e.target.files);
		const files = Array.from(e.target.files);
		console.log('Files array:', files);
		addFilesToQueue(files);
		e.target.value = ''; // Reset input
	}

	// Trigger file input click
	function triggerFileSelect() {
		if (fileInputElement) {
			fileInputElement.click();
		} else {
			// Fallback to getElementById
			const input = document.getElementById('file-input');
			if (input) {
				input.click();
			}
		}
	}

	// Add files to upload queue
	function addFilesToQueue(files) {
		uploadQueue.update(currentQueue => {
			let newQueue = [...currentQueue];
			for (const file of files) {
				// Validate file type
				if (!ALLOWED_TYPES.includes(file.type)) {
					alert(`Invalid file type: ${file.name}. Only MP4, WebM, and MOV files are allowed.`);
					continue;
				}

				// Validate file size
				if (file.size > MAX_FILE_SIZE) {
					alert(`File too large: ${file.name}. Maximum size is 1GB.`);
					continue;
				}

				// Check if file already in queue
				if (newQueue.some((item) => item.file.name === file.name && item.file.size === file.size)) {
					continue;
				}

				// Add to queue
				const newItem = {
					id: crypto.randomUUID(),
					file,
					status: 'waiting',
					progress: 0,
					bytesUploaded: 0,
					totalBytes: file.size,
					uploadId: null,
					totalChunks: Math.ceil(file.size / CHUNK_SIZE),
					chunksUploaded: 0,
					speed: 0,
					timeRemaining: null,
					error: null,
					title: file.name.replace(/\.[^/.]+$/, ''),
					description: '',
					is_featured: false,
					sort_order: 0
				};

				newQueue.push(newItem);
			}
			return newQueue;
		});
	}

	// Start pending uploads
	function startPendingUploads() {
		console.log('startPendingUploads called!');
		const maxConcurrent = 2; // Max 2 concurrent uploads
		uploadQueue.update(currentQueue => {
			console.log('Current queue in startPendingUploads:', currentQueue);
			const uploading = currentQueue.filter((item) => item.status === 'uploading');
			const pending = currentQueue.filter((item) => item.status === 'waiting');
			console.log('Uploading:', uploading.length, 'Pending:', pending.length);

			if (uploading.length < maxConcurrent) {
				const toStart = pending.slice(0, maxConcurrent - uploading.length);
				console.log('Starting uploads for:', toStart);

				for (const item of toStart) {
					startUpload(item);
				}
			}
			return currentQueue;
		});
	}

	// Start upload for a single file
	async function startUpload(uploadItem) {
		const itemId = uploadItem.id;

		uploadQueue.update(queue =>
			queue.map((item) =>
				item.id === itemId ? { ...item, status: 'uploading' } : item
			)
		);

		try {
			// Get current item from store
			let currentItem;
			uploadQueue.update(queue => {
				currentItem = queue.find(item => item.id === itemId);
				return queue;
			});

			// Initialize upload
			const initResult = await initializeUpload(currentItem);

			// Update store with uploadId and totalChunks
			uploadQueue.update(queue =>
				queue.map(item =>
					item.id === itemId
						? { ...item, uploadId: initResult.upload_id, totalChunks: initResult.total_chunks }
						: item
				)
			);

			// Upload chunks (reads from store, updates store internally)
			await uploadChunks(itemId);

			// Get updated item for finalization
			uploadQueue.update(queue => {
				currentItem = queue.find(item => item.id === itemId);
				return queue;
			});
			await finalizeUpload(currentItem);

			// Mark as complete
			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId ? { ...item, status: 'complete', progress: 100 } : item
				)
			);

			// Start next pending upload
			startPendingUploads();
		} catch (error) {
			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId
						? { ...item, status: 'failed', error: error.message }
						: item
				)
			);
		}
	}

	// Initialize upload on server
	async function initializeUpload(uploadItem) {
		const formData = new FormData();
		formData.append('action', 'initialize');
		formData.append('file', uploadItem.file);

		const response = await fetch(`/admin/videos/upload/${event.id}`, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
			},
			body: formData
		});

		if (!response.ok) {
			const contentType = response.headers.get('content-type');
			if (contentType && contentType.includes('application/json')) {
				const data = await response.json();
				throw new Error(data.message || 'Failed to initialize upload');
			} else {
				// Handle HTML error response (e.g., 413 Payload Too Large)
				const text = await response.text();
				if (response.status === 413) {
					throw new Error('File too large. Maximum upload size is 1GB.');
				}
				throw new Error(`Server error (${response.status}): Failed to initialize upload`);
			}
		}

		return await response.json();
	}

	// Upload file in chunks
	async function uploadChunks(itemId) {
		let currentItem;
		uploadQueue.update(queue => {
			currentItem = queue.find((item) => item.id === itemId);
			return queue;
		});

		const file = currentItem.file;
		const totalChunks = currentItem.totalChunks;
		const uploadId = currentItem.uploadId;
		let startTime = Date.now();

		for (let i = 0; i < totalChunks; i++) {
			// Check if paused
			uploadQueue.update(queue => {
				currentItem = queue.find((item) => item.id === itemId);
				return queue;
			});

			if (currentItem?.status === 'paused') {
				return; // Exit chunk upload loop
			}

			const start = i * CHUNK_SIZE;
			const end = Math.min(start + CHUNK_SIZE, file.size);
			const chunk = file.slice(start, end);

			const formData = new FormData();
			formData.append('action', 'chunk');
			formData.append('upload_id', uploadId);
			formData.append('chunk', chunk);
			formData.append('chunk_index', i.toString());
			formData.append('total_chunks', totalChunks.toString());

			const response = await fetch(`/admin/videos/upload/${event.id}`, {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
				},
				body: formData
			});

			if (!response.ok) {
				const contentType = response.headers.get('content-type');
				if (contentType && contentType.includes('application/json')) {
					const data = await response.json();
					throw new Error(data.message || 'Failed to upload chunk');
				} else {
					throw new Error(`Server error (${response.status}): Failed to upload chunk`);
				}
			}

			const result = await response.json();

			// Update progress
			const elapsed = (Date.now() - startTime) / 1000;
			const speed = result.bytes_received / elapsed;
			const remainingBytes = result.total_bytes - result.bytes_received;
			const timeRemaining = remainingBytes / speed;

			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId
						? {
								...item,
								chunksUploaded: result.chunks_received,
								bytesUploaded: result.bytes_received,
								progress: result.progress,
								speed,
								timeRemaining
						  }
						: item
				)
			);
		}
	}

	// Finalize upload
	async function finalizeUpload(uploadItem) {
		const formData = new FormData();
		formData.append('action', 'finalize');
		formData.append('upload_id', uploadItem.uploadId);
		formData.append('title', uploadItem.title);
		formData.append('description', uploadItem.description);
		formData.append('is_featured', uploadItem.is_featured ? '1' : '0');
		formData.append('sort_order', uploadItem.sort_order.toString());

		const response = await fetch(`/admin/videos/upload/${event.id}`, {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
			},
			body: formData
		});

		if (!response.ok) {
			const contentType = response.headers.get('content-type');
			if (contentType && contentType.includes('application/json')) {
				const data = await response.json();
				throw new Error(data.message || 'Failed to finalize upload');
			} else {
				throw new Error(`Server error (${response.status}): Failed to finalize upload`);
			}
		}

		return await response.json();
	}

	// Pause upload
	function pauseUpload(uploadItem) {
		uploadQueue.update(queue =>
			queue.map((item) =>
				item.id === uploadItem.id ? { ...item, status: 'paused' } : item
			)
		);

		// Start next pending upload
		startPendingUploads();
	}

	// Resume upload
	async function resumeUpload(uploadItem) {
		const itemId = uploadItem.id;

		uploadQueue.update(queue =>
			queue.map((item) =>
				item.id === itemId ? { ...item, status: 'uploading' } : item
			)
		);

		try {
			// Get current item from store
			let currentItem;
			uploadQueue.update(queue => {
				currentItem = queue.find((item) => item.id === itemId);
				return queue;
			});

			// Resume from server
			const formData = new FormData();
			formData.append('action', 'resume');
			formData.append('upload_id', currentItem.uploadId);

			const response = await fetch(`/admin/videos/upload/${event.id}`, {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
				},
				body: formData
			});

			if (!response.ok) {
				throw new Error('Failed to resume upload');
			}

			const result = await response.json();

			// Continue uploading missing chunks
			await uploadMissingChunks(itemId, result.missing_chunks);

			// Get updated item for finalization
			uploadQueue.update(queue => {
				currentItem = queue.find((item) => item.id === itemId);
				return queue;
			});
			await finalizeUpload(currentItem);

			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId ? { ...item, status: 'complete', progress: 100 } : item
				)
			);

			startPendingUploads();
		} catch (error) {
			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId
						? { ...item, status: 'failed', error: error.message }
						: item
				)
			);
		}
	}

	// Upload missing chunks after resume
	async function uploadMissingChunks(itemId, missingChunks) {
		let currentItem;
		uploadQueue.update(queue => {
			currentItem = queue.find((item) => item.id === itemId);
			return queue;
		});

		const file = currentItem.file;
		const uploadId = currentItem.uploadId;
		const totalChunks = currentItem.totalChunks;

		for (const chunkIndex of missingChunks) {
			const start = chunkIndex * CHUNK_SIZE;
			const end = Math.min(start + CHUNK_SIZE, file.size);
			const chunk = file.slice(start, end);

			const formData = new FormData();
			formData.append('action', 'chunk');
			formData.append('upload_id', uploadId);
			formData.append('chunk', chunk);
			formData.append('chunk_index', chunkIndex.toString());
			formData.append('total_chunks', totalChunks.toString());

			const response = await fetch(`/admin/videos/upload/${event.id}`, {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
				},
				body: formData
			});

			if (!response.ok) {
				throw new Error('Failed to upload chunk');
			}

			const result = await response.json();

			// Update progress
			uploadQueue.update(queue =>
				queue.map((item) =>
					item.id === itemId
						? {
								...item,
								chunksUploaded: result.chunks_received,
								bytesUploaded: result.bytes_received,
								progress: result.progress
						  }
						: item
				)
			);
		}
	}

	// Cancel upload
	async function cancelUpload(uploadItem) {
		if (uploadItem.uploadId) {
			try {
				const formData = new FormData();
				formData.append('action', 'cancel');
				formData.append('upload_id', uploadItem.uploadId);

				await fetch(`/admin/videos/upload/${event.id}`, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
					},
					body: formData
				});
			} catch (e) {
				console.error('Failed to cancel upload on server:', e);
			}
		}

		uploadQueue.update(queue => queue.filter((item) => item.id !== uploadItem.id));
		startPendingUploads();
	}

	// Retry failed upload
	function retryUpload(uploadItem) {
		// Reset state
		const resetItem = {
			...uploadItem,
			status: 'waiting',
			progress: 0,
			bytesUploaded: 0,
			chunksUploaded: 0,
			uploadId: null,
			error: null,
			speed: 0,
			timeRemaining: null
		};

		uploadQueue.update(queue =>
			queue.map((item) =>
				item.id === uploadItem.id ? resetItem : item
			)
		);

		startPendingUploads();
	}

	// Pause all uploads
	function pauseAll() {
		uploadQueue.update(queue =>
			queue.map((item) =>
				item.status === 'uploading' ? { ...item, status: 'paused' } : item
			)
		);
	}

	// Cancel all uploads
	async function cancelAll() {
		let queueToCancel;
		await uploadQueue.update(queue => {
			queueToCancel = queue;
			return queue;
		});

		for (const item of queueToCancel) {
			if (item.uploadId) {
				try {
					const formData = new FormData();
					formData.append('action', 'cancel');
					formData.append('upload_id', item.uploadId);

					await fetch(`/admin/videos/upload/${event.id}`, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
						},
						body: formData
					});
				} catch (e) {
					console.error('Failed to cancel upload:', e);
				}
			}
		}

		uploadQueue.set([]);
	}

	// Format bytes
	function formatBytes(bytes) {
		if (bytes === 0) return '0 Bytes';
		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
	}

	// Format time remaining
	function formatTimeRemaining(seconds) {
		if (!seconds || seconds === Infinity) return '';
		if (seconds < 60) return `~${Math.round(seconds)} sec remaining`;
		const mins = Math.ceil(seconds / 60);
		return `~${mins} min remaining`;
	}

	// Format speed
	function formatSpeed(bytesPerSecond) {
		if (!bytesPerSecond) return '';
		return `${formatBytes(bytesPerSecond)}/s`;
	}

	// Get status badge
	function getStatusBadge(item) {
		switch (item.status) {
			case 'waiting':
				return { bg: 'bg-[#eaeaea]', text: 'text-[#9b9b9b]', label: 'Waiting' };
			case 'uploading':
				return { bg: 'bg-[#fff3e6]', text: 'text-[#ff7607]', label: 'Uploading' };
			case 'paused':
				return { bg: 'bg-[#fef3c7]', text: 'text-[#f59e0b]', label: 'Paused' };
			case 'complete':
				return { bg: 'bg-[#d1fae5]', text: 'text-[#10b981]', label: 'Complete' };
			case 'failed':
				return { bg: 'bg-[#fee2e2]', text: 'text-[#ef4444]', label: 'Failed' };
			default:
				return { bg: 'bg-[#eaeaea]', text: 'text-[#9b9b9b]', label: 'Unknown' };
		}
	}

	// Get progress bar color
	function getProgressColor(item) {
		switch (item.status) {
			case 'uploading':
				return 'bg-[#ff7607]';
			case 'paused':
				return 'bg-[#f59e0b]';
			case 'complete':
				return 'bg-[#10b981]';
			case 'failed':
				return 'bg-[#ef4444]';
			default:
				return 'bg-[#eaeaea]';
		}
	}

	// Get item background
	function getItemBg(item) {
		switch (item.status) {
			case 'paused':
				return 'bg-[#fef3c7] bg-opacity-30';
			case 'complete':
				return 'bg-[#d1fae5] bg-opacity-30';
			case 'failed':
				return 'bg-[#fee2e2] bg-opacity-30';
			default:
				return '';
		}
	}

	// Update video metadata
	function updateMetadata(uploadItem, field, value) {
		uploadQueue.update(queue =>
			queue.map((item) =>
				item.id === uploadItem.id ? { ...item, [field]: value } : item
			)
		);
	}

	// Summary stats
	$: summaryStats = (() => {
		const queue = $uploadQueue;
		const total = queue.length;
		const complete = queue.filter((i) => i.status === 'complete').length;
		const uploading = queue.filter((i) => i.status === 'uploading').length;
		const paused = queue.filter((i) => i.status === 'paused').length;
		const waiting = queue.filter((i) => i.status === 'waiting').length;
		const failed = queue.filter((i) => i.status === 'failed').length;
		const totalBytes = queue.reduce((sum, i) => sum + i.totalBytes, 0);

		return { total, complete, uploading, paused, waiting, failed, totalBytes };
	})();
</script>

<svelte:head>
	<title>Upload Videos | NIOGG Admin</title>
</svelte:head>

<div class="flex min-h-screen bg-gray-100">
	<AdminSidebar />

	<!-- Main Content -->
	<main class="flex-1 flex flex-col min-w-0">
		<!-- Header -->
		<header class="bg-white border-b border-[#eaeaea] px-4 sm:px-6 lg:px-8 py-4">
			<div class="flex items-center gap-4">
				<a href="/admin/events/{event?.id}" class="text-[#9b9b9b] hover:text-[#1b1a1a]">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
				<div>
					<h1 class="text-xl sm:text-2xl font-bold text-[#1b1a1a]">Upload Videos</h1>
					<p class="text-sm text-[#9b9b9b]">{event?.name || 'Event'}</p>
				</div>
			</div>
		</header>

		<!-- Content -->
		<div class="p-4 sm:p-6 lg:p-8">
			<div class="max-w-4xl mx-auto">
				<!-- Upload Area -->
				<div class="bg-white rounded-lg border border-[#eaeaea] p-4 sm:p-6 lg:p-8 mb-6">
					<input
						bind:this={fileInputElement}
						id="file-input"
						type="file"
						accept="video/mp4,video/webm,video/quicktime"
						multiple
						class="hidden"
					/>
					<label
						for="file-input"
						class="border-2 border-dashed rounded-lg p-8 sm:p-12 text-center transition-colors cursor-pointer block {dropZoneActive
							? 'border-[#ff7607] bg-[#fff3e6]'
							: 'border-[#eaeaea] hover:border-[#ff7607]'}"
						ondragover={handleDragOver}
						ondragleave={handleDragLeave}
						ondrop={handleDrop}
					>
						<svg
							class="mx-auto h-16 w-16 text-[#9b9b9b] mb-4"
							fill="none"
							stroke="currentColor"
							viewBox="0 0 24 24"
						>
							<path
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
							/>
						</svg>
						<h3 class="text-lg font-medium text-[#1b1a1a] mb-2">
							Drop videos here or click to browse
						</h3>
						<p class="text-[#9b9b9b] mb-4">MP4, WebM, MOV up to 1 GB each • Multiple files supported</p>
						<span
							class="inline-block px-6 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium"
						>
							Select Files
						</span>
					</label>
				</div>

				<!-- Upload Queue -->
				{#if $uploadQueue.length > 0}
					<div class="bg-white rounded-lg border border-[#eaeaea]">
						<div class="px-4 sm:px-6 py-4 border-b border-[#eaeaea] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
							<h2 class="font-semibold text-[#1b1a1a]">Upload Queue ({$uploadQueue.length} files)</h2>
							<div class="flex items-center gap-2 w-full sm:w-auto">
								<button
									on:click={startPendingUploads}
									class="px-3 py-1 text-sm text-[#10b981] hover:text-[#059669] border border-[#10b981] rounded hover:bg-[#d1fae5]"
								>
									Start Upload
								</button>
								<button
									on:click={pauseAll}
									class="px-3 py-1 text-sm text-[#9b9b9b] hover:text-[#1b1a1a] border border-[#eaeaea] rounded hover:bg-[#f9f9f9]"
								>
									Pause All
								</button>
								<button
									on:click={cancelAll}
									class="px-3 py-1 text-sm text-[#ef4444] hover:text-[#dc2626] border border-[#eaeaea] rounded hover:bg-[#fee2e2]"
								>
									Cancel All
								</button>
							</div>
						</div>

						<div class="divide-y divide-[#f9f9f9]">
							{#each $uploadQueue as item (item.id)}
								{@const statusBadge = getStatusBadge(item)}
								{@const progressColor = getProgressColor(item)}
								{@const itemBg = getItemBg(item)}
								<div class="p-3 sm:p-4 {itemBg}">
									<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
										<!-- Video icon -->
										<div class="w-12 h-12 min-w-[48px] bg-[#1b1a1a] rounded flex items-center justify-center text-white {item.status ===
										'waiting' || item.status === 'paused'
											? 'opacity-50'
											: ''}">
											{#if item.status === 'complete'}
												<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M5 13l4 4L19 7"
													/>
												</svg>
											{:else if item.status === 'failed'}
												<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														stroke-width="2"
														d="M6 18L18 6M6 6l12 12"
													/>
												</svg>
											{:else}
												<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
													<path d="M8 5v14l11-7z" />
												</svg>
											{/if}
										</div>

										<div class="flex-1">
											<!-- Header with name and status -->
											<div class="flex items-center justify-between mb-1">
												<div class="flex items-center gap-2">
													<h3 class="font-medium text-[#1b1a1a] text-sm">{item.file.name}</h3>
													{#if item.status !== 'waiting'}
														<span
															class="px-2 py-0.5 {statusBadge.bg} {statusBadge.text} text-xs font-medium rounded"
														>
															{statusBadge.label}
														</span>
													{/if}
												</div>
												<span class="text-sm font-medium {item.status === 'complete'
													? 'text-[#10b981]'
													: item.status === 'failed'
														? 'text-[#ef4444]'
														: 'text-[#ff7607]'}"
												>
													{item.progress}%
												</span>
											</div>

											<!-- Progress bar -->
											<div class="w-full bg-[#f9f9f9] rounded-full h-2 mb-2">
												<div
													class="{progressColor} h-2 rounded-full transition-all"
													style="width: {item.progress}%"
												></div>
											</div>

											<!-- Details and actions -->
											<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-[#9b9b9b] w-full">
												<div class="flex-1">
													{#if item.status === 'waiting'}
														<span>Queued • {formatBytes(item.totalBytes)}</span>
													{:else if item.status === 'paused'}
														<span>{formatBytes(item.bytesUploaded)} / {formatBytes(item.totalBytes)}
															• Paused</span
														>
													{:else if item.status === 'complete'}
														<span>{formatBytes(item.totalBytes)} • Uploaded successfully</span>
													{:else if item.status === 'failed'}
														<span>{formatBytes(item.bytesUploaded)} / {formatBytes(item.totalBytes)}
															• {item.error || 'Upload failed'}</span
														>
													{:else}
														<span>{formatBytes(item.bytesUploaded)} / {formatBytes(item.totalBytes)}
															• {formatSpeed(item.speed)}</span
														>
														{#if item.timeRemaining}
															<span class="ml-2">{formatTimeRemaining(item.timeRemaining)}</span>
														{/if}
													{/if}
												</div>

												<!-- Action buttons -->
												<div class="flex items-center gap-2 flex-wrap">
													{#if item.status === 'uploading'}
														<button
															on:click={() => pauseUpload(item)}
															class="text-[#9b9b9b] hover:text-[#1b1a1a]"
															title="Pause"
														>
															<svg
																class="w-4 h-4"
																fill="none"
																stroke="currentColor"
																viewBox="0 0 24 24"
															>
																<path
																	stroke-linecap="round"
																	stroke-linejoin="round"
																	stroke-width="2"
																	d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"
																/>
															</svg>
														</button>
														<button
															on:click={() => cancelUpload(item)}
															class="text-[#ef4444] hover:text-[#dc2626]"
															title="Cancel"
														>
															Cancel
														</button>
													{:else if item.status === 'paused'}
														<button
															on:click={() => resumeUpload(item)}
															class="text-[#10b981] hover:text-[#059669] flex items-center gap-1"
															title="Resume"
														>
															<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
																<path d="M8 5v14l11-7z" />
															</svg>
															Resume
														</button>
														<button
															on:click={() => cancelUpload(item)}
															class="text-[#ef4444] hover:text-[#dc2626]"
															title="Cancel"
														>
															Cancel
														</button>
													{:else if item.status === 'failed'}
														<button
															on:click={() => retryUpload(item)}
															class="text-[#10b981] hover:text-[#059669] flex items-center gap-1"
															title="Retry"
														>
															<svg
																class="w-4 h-4"
																fill="none"
																stroke="currentColor"
																viewBox="0 0 24 24"
															>
																<path
																	stroke-linecap="round"
																	stroke-linejoin="round"
																	stroke-width="2"
																	d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
																/>
															</svg>
															Retry
														</button>
														<button
															on:click={() => cancelUpload(item)}
															class="text-[#ef4444] hover:text-[#dc2626]"
															title="Remove"
														>
															Remove
														</button>
													{:else if item.status === 'waiting'}
														<button
															on:click={() => cancelUpload(item)}
															class="text-[#ef4444] hover:text-[#dc2626]"
															title="Remove"
														>
															Remove
														</button>
													{:else if item.status === 'complete'}
														<a
															href="/admin/events/{event?.id}"
															class="text-[#ff7607] hover:text-[#e56a00]"
															title="View videos"
														>
															View
														</a>
													{/if}
												</div>
											</div>

											<!-- Metadata form for completed uploads -->
											{#if item.status === 'complete' || item.status === 'waiting'}
												<div class="mt-3 pt-3 border-t border-[#f9f9f9] grid grid-cols-1 sm:grid-cols-2 gap-3">
													<div>
														<label class="block text-xs font-medium text-[#1b1a1a] mb-1"
															>Title</label
														>
														<input
															type="text"
															bind:value={item.title}
															oninput={(e) => updateMetadata(item, 'title', e.target.value)}
															class="w-full px-2 py-1 text-sm border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] outline-none"
															placeholder="Video title"
														/>
													</div>
													<div>
														<label class="block text-xs font-medium text-[#1b1a1a] mb-1"
															>Sort Order</label
														>
														<input
															type="number"
															bind:value={item.sort_order}
															oninput={(e) => updateMetadata(item, 'sort_order', parseInt(e.target.value) || 0)}
															class="w-full px-2 py-1 text-sm border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] outline-none"
															placeholder="0"
														/>
													</div>
													<div class="col-span-2">
														<label class="block text-xs font-medium text-[#1b1a1a] mb-1"
															>Description</label
														>
														<textarea
															bind:value={item.description}
															oninput={(e) => updateMetadata(item, 'description', e.target.value)}
															rows="2"
															class="w-full px-2 py-1 text-sm border border-[#eaeaea] rounded focus:ring-2 focus:ring-[#ff7607] outline-none resize-none"
															placeholder="Video description (optional)"
														></textarea>
													</div>
												</div>
											{/if}
										</div>
									</div>
								</div>
							{/each}
						</div>

						<!-- Upload Summary -->
						<div class="px-4 sm:px-6 py-3 sm:py-4 bg-[#f9f9f9] border-t border-[#eaeaea]">
							<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-sm">
								<span class="text-[#9b9b9b]"
									>{summaryStats.complete} complete, {summaryStats.uploading} uploading,
									{summaryStats.paused} paused, {summaryStats.waiting} waiting,
									{summaryStats.failed} failed</span
								>
								<span class="text-[#1b1a1a] font-medium">Total: {formatBytes(summaryStats.totalBytes)}</span>
							</div>
						</div>
					</div>

					<!-- Bulk Upload Options -->
					<div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
						<div class="text-sm text-[#9b9b9b]">
							<p>Uploads will be organized in: <span class="text-[#1b1a1a] font-mono">/videos/{event?.slug ||
									'event'}/</span></p
							>
						</div>
						<a
							href="/admin/events/{event?.id}"
							class="px-6 py-2 bg-[#ff7607] text-white rounded-lg hover:bg-[#e56a00] font-medium"
						>
							Done (Close)
						</a>
					</div>
				{/if}

				{#if $uploadQueue.length === 0}
					<!-- Empty state -->
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
								d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
							/>
						</svg>
						<h3 class="text-lg font-medium text-[#1b1a1a] mb-2">No videos selected</h3>
						<p class="text-[#9b9b9b] mb-4">Drag and drop video files above or click to browse.</p>
						<a
							href="/admin/events/{event?.id}"
							class="inline-flex items-center gap-2 px-4 py-2 border border-[#eaeaea] text-[#1b1a1a] rounded-lg hover:bg-[#f9f9f9] font-medium text-sm"
						>
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
							</svg>
							Back to Event
						</a>
					</div>
				{/if}
			</div>
		</div>
	</main>
</div>

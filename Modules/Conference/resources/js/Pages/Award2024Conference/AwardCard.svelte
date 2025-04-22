<script lang="ts">
  import { Trophy } from 'lucide-svelte';
  import type { Award } from './types';

  export let award: Award;
  export let index: number;
</script>

<div
  class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl hover:translate-y-[-4px] group"
  style="animation-delay: {index * 100}ms; animation: fadeIn 0.5s ease-out forwards;"
>
  <div class="p-6 relative">
    <div class="absolute top-4 right-4 text-amber-500 opacity-70 transition-transform duration-300 group-hover:scale-110 group-hover:opacity-100">
      <Trophy size={24} />
    </div>

    <div class="mb-4">
      <span class="inline-block py-1 px-3 rounded-full text-xs font-medium bg-amber-100 text-amber-800 mb-2">
        {award.category}
      </span>
    </div>

    <h3 class="text-lg font-semibold text-gray-900 mb-1">
      {award.recipient}
    </h3>

    {#if award.organization}
      <p class="text-sm text-gray-600 mb-2">
        {award.organization}
      </p>
    {:else}
      <span class="mb-8 block"></span>
    {/if}
  </div>

  <div class="h-1 bg-gradient-to-r from-amber-300 to-amber-500"></div>
</div>

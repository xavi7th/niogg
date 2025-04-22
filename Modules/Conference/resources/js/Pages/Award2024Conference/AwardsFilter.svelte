<script lang="ts">
  import type { FilterCategory, SortOption } from './types';

  export let searchQuery: string;
  export let sortOption: SortOption;
  export let categories: string[];
  export let activeCategory: FilterCategory;
</script>

<div class="mb-8 space-y-4">
  <div class="flex flex-wrap gap-2">
    <button
      class="py-1 px-4 rounded-full text-sm font-medium transition-colors {activeCategory === null ? 'bg-amber-500 focus:bg-amber-500 text-white' : 'bg-gray-200 focus:bg-gray-200 hover:bg-gray-300 text-gray-700'}"
      on:click={() => activeCategory = null}
    >
      All Categories
    </button>

    {#each categories as category}
      <button
        class="py-1 px-4 rounded-full text-sm font-medium transition-colors {activeCategory === category ? 'bg-amber-500 focus:bg-amber-500 text-white' : 'bg-gray-200 focus:bg-gray-200 hover:bg-gray-300 text-gray-700'}"
        on:click={() => activeCategory = category}
      >
        {category}
      </button>
    {/each}
  </div>

  <div class="flex flex-col sm:flex-row gap-4">
    <div class="relative flex-grow">
      <input
        type="text"
        placeholder="Search awardees..."
        class="w-full py-2 pl-4 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
        bind:value={searchQuery}
      />
    </div>

    <div class="sm:w-48">
      <select
        class="w-full py-2 px-4 rounded-lg border border-gray-300 appearance-none bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
        bind:value={sortOption}
      >
        <option value="default">Default Order</option>
        <option value="recipient">Sort by Name</option>
        <option value="category">Sort by Category</option>
      </select>
    </div>
  </div>
</div>

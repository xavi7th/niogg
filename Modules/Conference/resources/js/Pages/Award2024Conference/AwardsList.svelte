<script lang="ts">
  import { awards } from './awards';
  import { ArrowDown } from 'lucide-svelte';
  import AwardCard from './AwardCard.svelte';
  import AwardsFilter from './AwardsFilter.svelte';
  import type { Award, FilterCategory, SortOption } from './types';

  let searchQuery = '';
  let sortOption: SortOption = 'category';
  let activeCategory: FilterCategory = null;

  $: categories = Array.from(new Set(awards.map(award => award.category)));

  /** @type {Award[]} */
  $: filteredAwards = awards
    .filter(award => {
      if (searchQuery) {
        const query = searchQuery?.toLowerCase() || '';
        return award.recipient.toLowerCase().includes(query) ||
               award.organization?.toLowerCase()?.includes(query) ||
               award.category.toLowerCase().includes(query);
      }
      return true;
    })
    .filter(award => !activeCategory || award.category === activeCategory)
    .sort((a, b) => {
      if (sortOption === 'recipient') return a.recipient.localeCompare(b.recipient);
      if (sortOption === 'category') return a.category.localeCompare(b.category);
      return 0;
    });
</script>

<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
  <AwardsFilter
    bind:searchQuery
    bind:sortOption
    bind:activeCategory
    {categories}
  />

  {#if filteredAwards.length > 0}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      {#each filteredAwards as award, index}
        <AwardCard {award} {index} />
      {/each}
    </div>
  {:else}
    <div class="text-center py-12">
      <p class="text-lg text-gray-600">No awards match your filters.</p>
      <button
        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500"
        on:click={() => {
          searchQuery = '';
          activeCategory = null;
          sortOption = 'default';
        }}
      >
        Reset Filters
      </button>
    </div>
  {/if}

  {#if filteredAwards.length > 6}
    <div class="flex justify-center mt-8">
      <a
        href="#top"
        class="inline-flex items-center text-amber-600 hover:text-amber-800 transition-colors"
      >
        <ArrowDown class="mr-2 transform rotate-180" size={16} />
        Back to top
      </a>
    </div>
  {/if}
</div>

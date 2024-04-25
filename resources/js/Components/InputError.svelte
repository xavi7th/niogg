<script>
  import { page } from "@inertiajs/svelte";
  $: ({ flash } = $page.props);

  export let message = '', className = 'mt-1';

  $: props = (({message, className, ...rest}) => rest)($$props);
</script>

<template>
  {#if message}
    {#key message}
      <p class="text-xs text-red-600 dark:text-red-400 { className }" {...props}>{message}</p>
    {/key}
  {:else if flash.error}
    <p class="text-xs text-red-600 dark:text-red-400 { className }" {...props}>{flash.error}</p>
  {/if}
</template>

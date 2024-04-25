<script>
  import { page } from "@inertiajs/svelte";
  $: ({ flash } = $page.props);

  export let message = '';

  $: props = (({message, ...rest}) => rest)($$props);
</script>

<template>
  {#if message}
    {#key message}
      <p class="text-sm text-red-600 dark:text-red-400" {...props}>{message}</p>
    {/key}
  {:else if flash.error}
    <p class="text-sm text-red-600 dark:text-red-400" {...props}>{flash.error}</p>
  {:else if flash.success}
    <p class="text-sm text-green-600 dark:text-green-400" {...props}>{flash.success}</p>
  {/if}
</template>

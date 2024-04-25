<script>
  export let label;

  $: props = (({label, ...rest}) => rest)($$props);
</script>

<template>
  <!-- svelte-ignore a11y-label-has-associated-control -->
  <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" {...props}>
    {#if label}
      <span>{ label }</span>
    {:else}
      <span><slot /></span>
    {/if}
  </label>
</template>

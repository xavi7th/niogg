<script>
  import { onMount } from "svelte";

  export let value = undefined;

  let input;

  onMount(() => {
    if (input.hasAttribute("autofocus")) {
      input.focus();
    }
  });
</script>

<template>
  <input bind:this={input} class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:focus:bg-gray-800 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring-indigo-300 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full " bind:value {...$$restProps}/>
</template>

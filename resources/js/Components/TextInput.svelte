<script>
  import { onMount } from "svelte";

  export let value = undefined, className = '', hasErrors = false, input;

  let defaultStyles = `
    border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:focus:bg-gray-800 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600
    focus:ring-indigo-300 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full [&.has-errors]:border-red-500
  `;

  onMount(() => {
    if (input.hasAttribute("autofocus")) {
      input.focus();
    }
  });
</script>

<!-- EXAMPLE USAGE -->
<!-- import TextInput from "@/Components/TextInput.svelte"; -->

<template>
  <input bind:this={input} class="{defaultStyles} {className}" class:has-errors={hasErrors} bind:value {...$$restProps} on:focus on:blur on:click on:dblclick on:mousedown on:mousemove on:mouseout on:mouseover on:mouseup on:change on:keypress on:keydown on:keyup/>
</template>

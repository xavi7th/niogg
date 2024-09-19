<script>
  import { cn } from "@/utils.js";
  import { onMount } from "svelte";

  export let value = undefined, hasErrors = false, input;

  export {className as class}

  let className = '';

  let defaultStyles = `
    border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:focus:bg-gray-800 dark:text-gray-300 focus:border-zinc-300 dark:focus:border-indigo-600
    focus:ring-zinc-300 focus-visible:ring-offset-2 dark:focus:ring-indigo-600 rounded-md shadow-sm block w-full [&.has-errors]:border-red-500
    disabled:cursor-not-allowed disabled:opacity-50 placeholder:text-muted-foreground
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
  <input bind:this={input} class="{ cn(defaultStyles, className) }" class:has-errors={hasErrors} bind:value {...$$restProps} on:focus on:blur on:click on:dblclick on:mousedown on:mousemove on:mouseout on:mouseover on:mouseup on:change on:keypress on:keydown on:keyup/>
</template>

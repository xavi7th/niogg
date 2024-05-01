<script>
  import { fly, fade } from 'svelte/transition'
  import { onMount, onDestroy } from "svelte";
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();

  export let show = false,
    maxWidth = "lg",
    closeable = true;

  $: {
    show ? document.body.style.overflow = "hidden" : document.body.style.overflow = null;
  }

  const close = () => {
    if (closeable) {
      dispatch("close");
    }
  };

  const closeOnEscape = (e) => {
    if (e.key === "Escape" && show) {
      close();
    }
  };

  const maxWidthClass = {
    sm: "sm:max-w-sm",
    md: "sm:max-w-md",
    lg: "sm:max-w-lg",
    xl: "sm:max-w-xl",
    "2xl": "sm:max-w-2xl",
  }[maxWidth];

  onMount(() => document.addEventListener("keydown", closeOnEscape));

  onDestroy(() => {
    document.removeEventListener("keydown", closeOnEscape);
    document.body.style.overflow = null;
  });
</script>

<template>
  {#if show}
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" scroll-region in:fade out:fade={{ delay: 150 }}>
      <div class="fixed inset-0 transform" on:click={close} on:keypress={close} role="button" tabindex="">
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"/>
      </div>

      <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform sm:w-full sm:mx-auto {maxWidthClass}" in:fly={{ y: -50, delay: 300 }} out:fly={{ y: -50 }}>
        <slot />
      </div>
    </div>
  {/if}
</template>

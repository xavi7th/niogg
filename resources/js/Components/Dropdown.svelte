<script>
  import { onMount, onDestroy } from "svelte";

  export let align = "right",
    widthClass = "w-48",
    contentClasses = "py-1 bg-white dark:bg-gray-700";

  let open;

  const closeOnEscape = (e) => {
    if (open && e.key === "Escape") {
      open = false;
    }
  };

  onMount(() => document.addEventListener("keydown", closeOnEscape));
  onDestroy(() => document.removeEventListener("keydown", closeOnEscape));

  const alignmentClasses = align === "left" ? "ltr:origin-top-left rtl:origin-top-right start-0" :( align === "right" ? "ltr:origin-top-right rtl:origin-top-left end-0" : "origin-top");
</script>

<template>
  <div class="relative">
    <div on:click={() => (open = !open)} on:keypress={() => (open = !open)} role="button" tabindex="">
      <slot name="trigger" />
    </div>

    <!-- Full Screen Dropdown Overlay -->
    <div class:hidden={ ! open } class="fixed inset-0 z-40" on:click={() => (open = false)} on:keypress={() => (open = false)} role="presentation"></div>

    <div class:hidden={ ! open } class="absolute z-50 mt-2 rounded-md shadow-lg {widthClass} {alignmentClasses}" on:click={() => (open = false)} on:keydown={() => (open = false)} role="presentation">
      <div class="rounded-md ring-1 ring-black ring-opacity-5 {contentClasses}">
        <slot name="content" />
      </div>
    </div>
  </div>
</template>

<script>
  import { Link, inertia } from "@inertiajs/svelte";

  export let href = "/",
    method = 'GET',
    asBtn = false;
</script>

<template>
  {#if asBtn}
    <button use:inertia={{ href, method }} type="button" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
      <slot />
    </button>
  {:else}
    <Link {href} {method} class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
      <slot />
    </Link>
  {/if}
</template>

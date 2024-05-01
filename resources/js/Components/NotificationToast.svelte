<script>
  import { afterUpdate } from "svelte";
  import { page } from "@inertiajs/svelte";
  import { getErrorString } from "@/helpers";
  import { fly, fade } from "svelte/transition";

  $: ({ flash, errors } = $page.props);
  $: if (Object.entries(flash).length || Object.entries(errors).length) showNotification = true;
  $:theme = flash?.success ? "teal" : flash?.warning ? "yellow" : flash?.info ? "blue" : "red";

  let showNotification = false;

  afterUpdate(() => {
    if (showNotification) {
      setTimeout(() => {
        showNotification = false;
      }, 300000);
    }
  })
</script>

<template>
  {#if showNotification}
    <!-- space-y-3 to allow stacking multiple notifications -->
    <div class="space-y-3" transition:fade={{ duration: 600 }}>
      <!-- Toast -->
      <div id="dismiss-toast" class="max-w-xs bg-{theme}-100 dark:bg-{theme}-800/10 text-{theme}-600 dark:text-{theme}-300 border border-gray-200 dark:border-{theme}-100/30 rounded-xl shadow-lg fixed top-3 right-4 z-10" role="alert" in:fly={{ x: 200, duration: 600 }} out:fade>
        <div class="flex flex-wrap p-4 box-content border-l-4 border-{theme}-500 rounded-lg">
          <div class="flex-shrink-0">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-{theme}-500 bg-{theme}-100 rounded-lg dark:bg-{theme}-800/50 dark:text-{theme}-200">
              <svg class="flex-shrink-0 size-4 text-{theme}-500 dark:text-{theme}-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                {#if flash.success}
                  <!-- Teal success icon -->
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
                {:else if flash.warning}
                  <!-- Yellow warning icon -->
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                {:else if flash.info}
                  <!-- Blue info icon -->
                  <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                {:else if flash.error || Object.entries(errors).length}
                  <!-- Red error icon -->
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"></path>
                {:else}
                  <!-- Colorless bell icon -->
                  <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
                {/if}
              </svg>
            </div>

            <button type="button" class="absolute top-3 end-3 inline-flex flex-shrink-0 justify-center items-center size-5 rounded-lg text-gray-800 opacity-50 hover:opacity-100 focus:outline-none focus:opacity-100 dark:text-white" data-hs-remove-element="#dismiss-toast" on:click={() => (showNotification = false)}>
              <span class="sr-only">Close</span>
              <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
              </svg>
            </button>
          </div>

          <div class="ms-4">
            <!-- Notification header -->
            <h3 class="text-{theme}-800 font-semibold dark:text-{theme}-500 text-lg">
              {#if flash.success}
                Success
              {:else if flash.warning || flash.info}
                Note!
              {:else}
                Error!
              {/if}
            </h3>
          </div>

          <!-- Notification body -->
          <hr class="w-full basis-full">
          <div class="mt-1 pl-12 text-sm">
            {@html flash.success || flash.warning || flash.error || flash.info || getErrorString(errors)}
          </div>

          <!-- Notification footer Slot footer -->
          <!-- <div class="mt-4">
            <div class="flex space-x-3">
              <button type="button" class="text-blue-600 decoration-2 hover:underline font-medium text-sm focus:outline-none focus:underline dark:text-blue-500">
                Don't allow
              </button>
              <button type="button" class="text-blue-600 decoration-2 hover:underline font-medium text-sm focus:outline-none focus:underline dark:text-blue-500">
                Allow
              </button>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  {/if}
</template>

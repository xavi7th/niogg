<script context="module">
  import PageLayout from "@appuser-pages/Layouts/PageLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  export let phpVersion = undefined;
</script>

<main>
  This is the AppUser Index page

  <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-end sm:ms-0">
    PHP v{phpVersion}
  </div>
</main>

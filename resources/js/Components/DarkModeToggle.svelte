<!-- Usage -->
<!-- import DarkModeToggle from "@/Components/DarkModeToggle.svelte"; -->

<script>
  import { cn } from "@/utils.js";
  import { Icons } from "@/Components/icons";

  export {className as class};

  let className = '';

  let setDarkMode = () => {
    localStorage.theme = 'dark';
    document.documentElement.classList.add('dark');
  }

  let setLightMode = () => {
    localStorage.theme = 'light';
    document.documentElement.classList.remove('dark')
  }
</script>

<div class={ cn('flex relative items-center justify-center', className) }>
  <button type="button" class="absolute top-0 inline-flex items-center justify-center size-[46px] rounded-full font-medium text-gray-800 hover:bg-white/75 focus:bg-white/75 focus:outline-none visible dark:invisible dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" on:click={setDarkMode}>
    <span class="group inline-flex size-9 shrink-0 items-center justify-center">
      {@html Icons.cresentMoon}
    </span>
  </button>

  <button type="button" class="absolute top-0 inline-flex items-center justify-center size-[46px] rounded-full font-medium text-gray-800 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none invisible dark:visible dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" on:click={setLightMode}>
    <span class="group inline-flex size-9 shrink-0 items-center justify-center">
      {@html Icons.sun}
    </span>
  </button>
</div>

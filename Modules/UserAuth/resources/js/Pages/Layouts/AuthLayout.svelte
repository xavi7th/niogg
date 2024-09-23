<script>
  import { Link } from '@inertiajs/svelte';
  import { pageTitle, pageDescription } from "@/stores";
  import ApplicationLogo from '@/Components/ApplicationLogo.svelte';
  import DarkModeToggle from "@/Components/DarkModeToggle.svelte";
</script>

<svelte:head>
	<title>{ $pageTitle }</title>
	<meta name="description" content={ $pageDescription || $pageTitle } />
</svelte:head>

<div class="w-full h-dvh flex lg:grid lg:min-h-[600px] lg:grid-cols-2 xl:min-h-[800px] items-center">
  <div class="flex flex-col items-center justify-center gap-8 lg:gap-4 mx-auto py-12">

    <div class="flex items-center mt-4 absolute top-1">
      <p class="mb-0 -ml-14">Switch to <span class="dark:hidden">Dark</span> <span class="hidden dark:inline-block">Light</span> Mode: </p>
      <DarkModeToggle class="ml-8" />
    </div>

    <div>
      <Link href="/">
        <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
      </Link>
    </div>

    <slot />

  </div>

  <div class="bg-muted hidden lg:block min-h-full">
    <enhanced:img src="@publicpage-assets/images/src/banners/nigeria-region.jpg?enhanced&h=1080" class="h-dvh w-full object-cover dark:brightness-[0.2] dark:grayscale" alt="banner thumb"  width="1920" height="1080"/>
  </div>
</div>

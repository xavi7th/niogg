<script>
	import { onMount } from "svelte";
  import { Link, page } from '@inertiajs/svelte';
  import { pageTitle, pageDescription } from "@/stores";
  import ApplicationLogo from '@/Components/ApplicationLogo.svelte';
  import NotificationToast from '@/Components/NotificationToast.svelte';

  $: ({ app } = $page.props);
</script>

<svelte:head>
	<title>{ $pageTitle }</title>
	<meta name="description" content={ $pageDescription || $pageTitle } />
</svelte:head>

<div class="w-full h-dvh lg:grid lg:min-h-[600px] lg:grid-cols-2 xl:min-h-[800px]">
  <div class="flex flex-col items-center justify-center gap-4 py-12">

    <div>
      <Link href="/">
        <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
      </Link>
    </div>

    <slot />

  </div>

  <div class="bg-muted hidden lg:block">
    <enhanced:img src="@publicpage-assets/images/src/banners/nigeria-region.jpg?enhanced&w=1920" class="h-full w-full object-cover dark:brightness-[0.2] dark:grayscale" alt="banner thumb"  width="1920" height="1080"/>
  </div>
</div>

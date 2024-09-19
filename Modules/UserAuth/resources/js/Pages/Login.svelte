<script context="module">
  import PageLayout from "@userauth-pages/Layouts/AuthLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import Checkbox from "@/Components/Checkbox.svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import { Input } from "@/Components/ui/input/index.js";
  import { Label } from "@/Components/ui/label/index.js";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import { Button } from "@/Components/ui/button/index.js";
  import { router, page, inertia } from "@inertiajs/svelte";
  import LoaderCircle from "lucide-svelte/icons/loader-circle";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  export let canResetPassword = false, title = '';

  $: ({ errors } = $page.props);

  let details = {},
    isLoading = false;

  const handleSubmit = () => {
    isLoading = true;

    router.post(route("auth.login"), details, {
      onFinish: () => (isLoading = false),
    });
  };

  pageTitle.update((t) => title || "Login to Access Dashboard");
</script>

<div class="mx-auto grid w-[350px] gap-6">
  <div class="grid gap-2 text-center">
    <h1 class="text-3xl font-bold">Login</h1>
    <p class="text-muted-foreground text-balance">
      Enter your email below to access the conference registrants information and payment status
    </p>
  </div>
  <form class="grid gap-4" on:submit|preventDefault={handleSubmit}>
    <div class="grid gap-2">
      <InputLabel for="email" label="Email" />
      <TextInput id="email" type="email" bind:value={details.email} required autofocus autocomplete="email" hasErrors={errors.email} placeholder="example@niogg.org"/>
      <InputError className="mt-1" message={errors.email} />
    </div>

    <div class="grid gap-2">
      <InputLabel for="password" label="Password" />
      <TextInput id="password" type="password" bind:value={details.password} required autocomplete="current-password" hasErrors={errors.password} />
      <InputError className="-mt-2 text-right" message={errors.password} />
    </div>

    <div class="block grid gap-2 mt-2">
      <div class="flex items-center justify-between">
        <Checkbox name="remember" bind:checked={details.remember} label="Remember me"/>
        {#if canResetPassword}
          <a use:inertia href={window.route("auth.password.request")} class="ml-auto inline-block text-sm underline">
            Forgot your password?
          </a>
        {/if}
      </div>
    </div>

    <Button type="submit" class="w-full dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-neutral-400  {isLoading ? 'opacity-25' : ''}" disabled={isLoading}>
      Login
      {#if isLoading}
        <LoaderCircle class="mr-2 h-4 w-4 animate-spin" />
      {/if}
    </Button>
  </form>
</div>

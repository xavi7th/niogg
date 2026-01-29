<script context="module">
  import PageLayout from "@userauth-pages/Layouts/GuestLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import { Link, router, page } from "@inertiajs/svelte";
  import Checkbox from "@/Components/Checkbox.svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";

  export let canResetPassword = false;

  $: ({ errors } = $page.props);

  let details = {},
    isLoading = false;

  const handleSubmit = () => {
    isLoading = true;

    router.post(route("auth.login"), details, {
      onFinish: () => (isLoading = false),
    });
  };

  pageTitle.update((title) => "Login to Access Dashboard");
</script>

<template>
  <form on:submit|preventDefault={handleSubmit} class="space-y-5">
    <!-- Email Field -->
    <div>
      <InputLabel for="email" label="Email Address" class="text-gray-700 dark:text-gray-300 font-medium" />
      <TextInput id="email" type="email" bind:value={details.email} required autofocus autocomplete="email" hasErrors={errors.email} />
      <InputError className="mt-2" message={errors.email} />
    </div>

    <!-- Password Field -->
    <div>
      <div class="flex items-center justify-between">
        <InputLabel for="password" value="Password" class="text-gray-700 dark:text-gray-300 font-medium" />
        {#if canResetPassword}
          <Link href={window.route("auth.password.request")} class="text-sm font-medium text-orange-600 hover:text-orange-500 dark:text-orange-400 dark:hover:text-orange-300 transition-colors">
            Forgot password?
          </Link>
        {/if}
      </div>
      <TextInput id="password" type="password" bind:value={details.password} required autocomplete="current-password" />
      <InputError className="mt-2" message={errors.password} />
    </div>

    <!-- Remember Me -->
    <div class="flex items-center">
      <Checkbox name="remember" bind:checked={details.remember} label="Remember me for 30 days" class="text-gray-600 dark:text-gray-400" />
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
      <button
        type="submit"
        disabled={isLoading}
        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 transition-all duration-200 {isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-0.5'}"
      >
        {#if isLoading}
          <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Signing in...
        {:else}
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
          </svg>
          Sign In
        {/if}
      </button>
    </div>
  </form>
</template>

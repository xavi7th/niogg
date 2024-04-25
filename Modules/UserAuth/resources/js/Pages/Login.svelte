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
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  export let canResetPassword = false,
    status = "";

  $: ({ errors } = $page.props);

  let details = {},
    isLoading = false;

  const handleSubmit = () => {
    isLoading = true;

    router.post(route("auth.login"), {
      onFinish: () => (isLoading = false),
    });
  };

  pageTitle.update((title) => "Login to Access Dashboard");
</script>

<template>
  {#if status}
    <div class="mb-4 font-medium text-sm text-green-600">{status}</div>
  {/if}

  <form on:submit|preventDefault={handleSubmit}>
    <div>
      <InputLabel for="email" label="Email" />
      <TextInput id="email" type="email" bind:val={details.email} required autofocus autocomplete="email" />

      <InputError class="mt-2" message={errors.email} />
    </div>

    <div class="mt-4">
      <InputLabel for="password" value="Password" />

      <TextInput id="password" type="password" bind:val={details.password} required autocomplete="current-password" />

      <InputError class="mt-2" message={errors.password} />
    </div>

    <div class="block mt-4">
      <Checkbox name="remember" bind:checked={details.remember} label="Remember me"/>
    </div>

    <div class="flex items-center justify-end mt-4">
      {#if canResetPassword}
        <Link href={route("auth.password.request")} class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 px-4" >Forgot your password?</Link>
      {/if}

      <PrimaryButton class="ms-4 {isLoading ? 'opacity-25' : ''}" disabled={isLoading}>Log in</PrimaryButton>
    </div>
  </form>
</template>

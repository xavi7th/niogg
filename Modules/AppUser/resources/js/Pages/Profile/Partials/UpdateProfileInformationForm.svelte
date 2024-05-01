<script>
  import { fly } from "svelte/transition";
  import TextInput from "@/Components/TextInput.svelte";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import { Link, useForm, page } from "@inertiajs/svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  $: ({ flash, auth } = $page.props);

  export let must_verify_email;

  const form = useForm({
    name: auth?.user.name,
    email: auth?.user.email,
  });
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update your account's profile information and email address.</p>
    </header>

    <form on:submit|preventDefault|stopPropagation={$form.patch(window.route("appuser.profile.update"))} class="mt-6 space-y-6">
      <div>
        <InputLabel for="name" label="Name" />
        <TextInput id="name" type="text" bind:value={$form.name} required autofocus autocomplete="name" hasErrors={$form.errors.name} />
        <InputError class="mt-2" message={$form.errors.name} />
      </div>

      <div>
        <InputLabel for="email" label="Email" />
        <TextInput id="email" type="email" bind:value={$form.email} required autocomplete="username" hasErrors={$form.errors.email} />
        <InputError class="mt-2" message={$form.errors.email} />
      </div>

      {#if must_verify_email}
        <div>
          <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
            Your email address is unverified.
            <Link
              href={window.route("auth.verification.send")}
              method="post"
              as="button"
              class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
            >
              Click here to re-send the verification email.
            </Link>
          </p>

          {#if flash.success == "Verification link sent"}
            <div class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">A new verification link has been sent to your email address.</div>
          {/if}
        </div>
      {/if}

      <div class="flex items-center gap-4">

        <PrimaryButton disabled={$form.processing}>Save</PrimaryButton>
        {#if $form.recentlySuccessful}
          <p class="text-sm text-gray-600 dark:text-gray-400" transition:fly={{ y: 100, duration: 300 }}>Saved.</p>
        {/if}
      </div>
    </form>
  </section>
</template>

<script context="module">
  import PageLayout from "@userauth-pages/Layouts/AuthLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import { Link, useForm } from "@inertiajs/svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const handleSubmit = () => {
    $form.post(route("auth.register"), {
      onFinish: () => $form.reset("password", "password_confirmation"),
    });
  };

  pageTitle.update((title) => "Register to create a new account");
</script>

<template>
  <form on:submit|preventDefault={handleSubmit}>
    <div>
      <InputLabel for="name" label="Full Name" />
      <TextInput id="name" type="text" bind:value={$form.name} required autofocus autocomplete="name" hasErrors={$form.errors.name} />
      <InputError className="mt-2" message={$form.errors.name} />
    </div>

    <div class="mt-4">
      <InputLabel for="email" value="Email" />
      <TextInput id="email" type="email" bind:value={$form.email} required autocomplete="email" hasErrors={$form.errors.email} />
      <InputError className="mt-2" message={$form.errors.email} />
    </div>

    <div class="mt-4">
      <InputLabel for="password" value="Password" />
      <TextInput id="password" type="password" bind:value={$form.password} required autocomplete="new-password" hasErrors={$form.errors.password} />
      <InputError className="mt-2" message={$form.errors.password} />
    </div>

    <div class="mt-4">
      <InputLabel for="password_confirmation" value="Confirm Password" />
      <TextInput id="password_confirmation" type="password" bind:value={$form.password_confirmation} required autocomplete="new-password" />
      <InputError className="mt-2" message={$form.errors.password_confirmation} />
    </div>

    <div class="flex items-center justify-end mt-4">
      <Link href={window.route("auth.login")} class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 px-4">
        Already registered?
      </Link>

      <PrimaryButton class="ms-4 {$form.processing ? 'opacity-25' : ''}" disabled={$form.processing}>Register</PrimaryButton>
    </div>
  </form>
</template>

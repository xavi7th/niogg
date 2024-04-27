<script context="module">
  import PageLayout from "@userauth-pages/Layouts/GuestLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import { useForm } from "@inertiajs/svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import InputError from "@/Components/InputError.svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  export let email = "",
    token = "";

  const form = useForm({
    token: token,
    email: email,
    password: "",
    password_confirmation: "",
  });

  const submit = () => {
    $form.post(route("auth.password.store"), {
      onFinish: () => $form.reset("password", "password_confirmation"),
    });
  };

  pageTitle.update((title) => "Choose a New Password");
</script>

<template>
  <form on:submit|preventDefault|stopPropagation={submit}>
    <div>
      <InputLabel for="email" label="Email" />
      <TextInput id="email" type="email" bind:value={$form.email} required autofocus autocomplete="username" hasErrors={$form.errors.email} />
      <InputError className="mt-2" message={$form.errors.email} />
    </div>

    <div class="mt-4">
      <InputLabel for="password" label="Password" />
      <TextInput id="password" type="password" bind:value={$form.password} required autocomplete="new-password" hasErrors={$form.errors.password} />
      <InputError className="mt-2" message={$form.errors.password} />
    </div>

    <div class="mt-4">
      <InputLabel for="password_confirmation" value="Confirm Password" />
      <TextInput id="password_confirmation" type="password" bind:value={$form.password_confirmation} required autocomplete="new-password" />
      <InputError className="mt-2" message={$form.errors.password_confirmation} />
    </div>

    <div class="flex items-center justify-end mt-4">
      <PrimaryButton className={$form.processing ? "opacity-25" : ""} disabled={$form.processing}>Reset Password</PrimaryButton>
    </div>
  </form>
</template>

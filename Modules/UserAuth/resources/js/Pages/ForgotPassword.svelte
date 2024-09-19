<script context="module">
  import PageLayout from "@userauth-pages/Layouts/AuthLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageTitle } from "@/stores";
  import { useForm } from "@inertiajs/svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  const form = useForm({
    email: "",
  });

  const handleSubmit = () => {
    $form.post(route("auth.password.email"));
  };

  pageTitle.update((title) => "Reset your password");
</script>

<template>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
  </div>

  <form on:submit|preventDefault|stopPropagation={handleSubmit}>
    <div>
      <InputLabel for="email" value="Email" />
      <TextInput id="email" type="email" bind:value={$form.email} required autofocus autocomplete="email" hasErrors={$form.errors.email} />
      <InputError className="mt-2" message={$form.errors.email} />
    </div>

    <div class="flex items-center justify-end mt-4">
      <PrimaryButton className={$form.processing ? "opacity-25" : ""} disabled={$form.processing}>Email Password Reset Link</PrimaryButton>
    </div>
  </form>
</template>

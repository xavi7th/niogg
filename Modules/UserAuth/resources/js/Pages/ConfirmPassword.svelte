<script context="module">
  import PageLayout from "@userauth-pages/Layouts/GuestLayout.svelte";
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
    password: "",
  });

  const submit = () => {
    $form.post(route("auth.password.confirm"), {
      onFinish: () => $form.reset(),
    });
  };

  pageTitle.update((title) => "Confirm Password");
</script>

<template>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">This is a secure area of the application. Please confirm your password before continuing.</div>

  <form on:submit|preventDefault|stopPropagation={submit}>
    <div>
      <InputLabel for="password" label="Password" />
      <TextInput id="password" type="password" bind:value={$form.password} required autocomplete="current-password" autofocus hasErrors={$form.errors.password}/>
      <InputError message={$form.errors.password} />
    </div>

    <div class="flex justify-end mt-4">
      <PrimaryButton class="ms-4 {$form.processing ? 'opacity-25' : ''}" disabled={$form.processing}>Confirm</PrimaryButton>
    </div>
  </form>
</template>

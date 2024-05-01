<script>
  import { fly } from "svelte/transition";
  import { useForm } from "@inertiajs/svelte";
  import TextInput from "@/Components/TextInput.svelte";
  import InputError from "@/Components/InputError.svelte";
  import InputLabel from "@/Components/InputLabel.svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  let passwordInput, currentPasswordInput;

  const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
  });

  const updatePassword = () => {
    $form.put(route("auth.password.update"), {
      preserveScroll: true,
      onSuccess: () => $form.reset(),
      onError: () => {
        if ($form.errors.password) {
          $form.reset("password", "password_confirmation");
          passwordInput.focus();
        }
        if ($form.errors.current_password) {
          $form.reset("current_password");
          currentPasswordInput.focus();
        }
      },
    });
  };
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Update Password</h2>
      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
    </header>

    <form on:submit|preventDefault|stopPropagation={updatePassword} class="mt-6 space-y-6">
      <div>
        <InputLabel for="current_password" label="Current Password" />
        <TextInput id="current_password" bind:input={currentPasswordInput} bind:value={$form.current_password} type="password" autofocus autocomplete="current-password"  hasErrors={$form.errors.current_password}/>
        <InputError message={$form.errors.current_password} />
      </div>

      <div>
        <InputLabel for="password" label="New Password" />
        <TextInput id="password" bind:input={passwordInput} bind:value={$form.password} type="password" autocomplete="new-password" hasErrors={$form.errors.password}/>
        <InputError message={$form.errors.password} />
      </div>

      <div>
        <InputLabel for="password_confirmation" label="Confirm Password" />
        <TextInput id="password_confirmation" bind:value={$form.password_confirmation} type="password" autocomplete="new-password" hasErrors={$form.errors.password_confirmation}/>
        <InputError message={$form.errors.password_confirmation} />
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton disabled={$form.processing}>Save</PrimaryButton>
        {#if $form.recentlySuccessful}
          <p class="text-sm text-teal-600 dark:text-teal-400" transition:fly={{ x: 20 }}>Saved.</p>
        {/if}
      </div>
    </form>
  </section>
</template>

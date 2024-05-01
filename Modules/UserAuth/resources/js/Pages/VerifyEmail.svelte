<script context="module">
  import PageLayout from "@userauth-pages/Layouts/GuestLayout.svelte";
  export const layout = PageLayout;
</script>


<script>
  import { pageTitle } from "@/stores";
  import { useForm, page, inertia } from "@inertiajs/svelte";
  import PrimaryButton from "@/Components/PrimaryButton.svelte";

  $: ({ flash } = $page.props);

  const form = useForm({});

  const handleSubmit = () => {
    $form.post(route("auth.verification.send"));
  };

  pageTitle.update((title) => "Email Verification");
</script>

<template>
  <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
    Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
  </div>

  {#if flash.success}
    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">A new verification link has been sent to the email address you provided during registration.</div>
  {/if}

  <form on:submit|preventDefault|stopPropagation={handleSubmit}>
    <div class="mt-4 flex items-center justify-between">
      <PrimaryButton className={$form.processing ? "opacity-25" : ""} disabled={$form.processing}>Resend Verification Email</PrimaryButton>

      <button use:inertia={{ href: window.route("auth.logout"), method: "post" }} type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" >
        Logout
      </button>
    </div>
  </form>
</template>

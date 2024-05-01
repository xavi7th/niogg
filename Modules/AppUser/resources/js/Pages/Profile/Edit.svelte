<script context="module">
  import PageLayout from "@appuser-pages/Layouts/AuthenticatedLayout.svelte";
  export const layout = PageLayout;
</script>

<script>
  import { pageHeader, pageTitle } from "@/stores";
  import DeleteUserForm from "@appuser-pages/Profile/Partials/DeleteUserForm.svelte";
  import UpdatePasswordForm from "@appuser-pages/Profile/Partials/UpdatePasswordForm.svelte";
  import UpdateProfileInformationForm from "@appuser-pages/Profile/Partials/UpdateProfileInformationForm.svelte";

  export let must_verify_email = false;

  pageTitle.update((title) => "Profile");
  pageHeader.update((header) => '<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Profile</h2>');
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <UpdateProfileInformationForm {must_verify_email} class="max-w-xl" />
      </div>

      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <UpdatePasswordForm class="max-w-xl" />
      </div>

      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <DeleteUserForm class="max-w-xl" />
      </div>
    </div>
  </div>
</template>

<script>
  import { tick } from 'svelte';
  import { useForm } from '@inertiajs/svelte';
  import Modal from '@/Components/Modal.svelte';
  import TextInput from '@/Components/TextInput.svelte';
  import InputError from '@/Components/InputError.svelte';
  import InputLabel from '@/Components/InputLabel.svelte';
  import DangerButton from '@/Components/DangerButton.svelte';
  import SecondaryButton from '@/Components/SecondaryButton.svelte';

let confirmingUserDeletion = false, passwordInput = null;

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion = true;

    tick(() => passwordInput.focus());
};

const deleteUser = () => {
    $form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.focus(),
        onFinish: () => $form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion = false;

    $form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Delete Account</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
                your account, please download any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton on:click={confirmUserDeletion}>Delete Account</DangerButton>

        <Modal show="{confirmingUserDeletion}" on:close={ closeModal }>
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please
                    enter your password to confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" label="Password" className="sr-only" />
                    <TextInput id="password" bind:this={ passwordInput } bind:value={ $form.password } type="password" class="mt-1 block w-3/4" placeholder="Password" on:keyup="{e => e.key ===' Enter' && deleteUser }" hasErrors={$form.errors.password}/>
                    <InputError message="{ $form.errors.password }" className="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton on:click={ closeModal }> Cancel </SecondaryButton>

                    <DangerButton className="ms-3 {$form.processing ? 'opacity-25' : '' }" disabled={ $form.processing } on:click={ deleteUser }>
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

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
  import { Button } from "@/Components/ui/button/index.js";
  import LoaderCircle from "lucide-svelte/icons/loader-circle";

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

<div class="grid gap-2 text-center">
  <p class="text-muted-foreground text-balance">
    This is a secure area of the application. Please confirm your password before continuing.
  </p>
</div>

<form on:submit|preventDefault|stopPropagation={submit}>
  <div>
    <InputLabel for="password" label="Password" />
    <TextInput id="password" type="password" bind:value={$form.password} required autocomplete="current-password" autofocus hasErrors={$form.errors.password}/>
    <InputError message={$form.errors.password} />
  </div>

  <div class="flex justify-end mt-4">
    <Button type="submit" class="ms-4 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-neutral-200" disabled={$form.processing || !$form.password}>
      Confirm
      {#if $form.processing}
        <LoaderCircle class="mr-2 h-4 w-4 animate-spin" />
      {/if}
    </Button>
  </div>
</form>

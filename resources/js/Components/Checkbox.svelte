<script>
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();
  let checked = false;

  export let label = "I need a label",
    name,
    className = "",
    errors = [];

  const handleChange = () => {
    checked = !checked;
    dispatch("change", checked);
  };

  $: props = (({ onChange, label, name, className, errors, type, ...rest }) => rest)($$props);

  /**
   * ! Whatever you export like this can be bound to in the component eg bind:checked={details.property}
   */
  export { checked };
</script>

<!-- Example Usage -->
<!-- Given this function defined in the parent
function handleChange({ target: { name, value } }) {
  details = {
      ...details,
      [name]: value
  };
}
ALT: Use emit to emit the value and accept it to update what you need to update
-->

<!-- <CheckBoxInput className="uk-margin-small uk-width-auto uk-text-small" label="Remember me" name="remember" errors={errors.remember} bind:checked={details.remember}/> -->
<!-- <CheckBoxInput className="uk-margin-small uk-width-auto uk-text-small" label="Remember me" name="remember" errors={errors.remember} on:change={(e) => details.remember = e.detail.checked }/> -->

<div class={className}>
  <label class="flex items-center" for={name}>
    <input id={name} {name} autocomplete={name} type="checkbox" {...props} on:change={handleChange} class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" class:error={errors && errors.length}/>
    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{label}</span>
  </label>

  {#if errors && errors.length}
    <div class="form-error">{errors}</div>
  {/if}
</div>

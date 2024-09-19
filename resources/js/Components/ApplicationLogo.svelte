<script>
  let className = undefined;

  export {className as class};
</script>

<template>
  <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-light.png?enhanced&w=80" class="logo-light {className}" alt="logo"/>
  <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-dark.png?enhanced&w=70" class="logo-dark {className}" alt="logo"/>
</template>

<style global lang="scss">
  html.dark{
    .logo-dark{
      display: none;
    }
  }

  html:not(.dark){
    .logo-light{
      display: none;
    }
  }
</style>

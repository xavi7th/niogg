<template>
  <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-light.png?enhanced&w=80" class="logo-light" alt="logo"/>
  <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-dark.png?enhanced&w=70" class="logo-dark" alt="logo"/>
</template>

<style global lang="scss">
  body.dark{
    .logo-dark{
      display: none;
    }
  }

  body:not(.dark){
    .logo-light{
      display: none;
    }
  }
</style>

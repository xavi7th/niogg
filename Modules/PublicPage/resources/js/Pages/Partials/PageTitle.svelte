<script>
  import { getImgUrl } from '@/helpers';
  export let appName = '', pageTitle = '';
</script>

<section id="pageTitle" class="page-title bg-overlay bg-parallax mb-24">
  <div class="bg-img">
    <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/placeholders/7.jpg') }" alt="background" />
  </div>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 text-center">
        <enhanced:img src="@publicpage-assets/images/src/logo/niogg-logo-light.png?enhanced&w=70&aspect=1:1" alt="logo"/>
        <span class="pagetitle__subheading">{appName}</span>
        <h1 class="pagetitle__heading">{pageTitle}</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{ window.route('app.index') }">Home</a></li>

            <slot />

          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  :global{
    #pageTitle picture{
      display: flex;
      justify-content: center;
      margin-bottom: 10px;
    }
  }

  .breadcrumb{
    justify-content: center;
  }
</style>

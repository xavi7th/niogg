<script>
  import { getImgUrl } from '@/helpers';
  export let appName = '', pageTitle = '';
</script>

<section id="pageTitle" class="page-title bg-overlay bg-parallax mb-0">
  <div class="bg-img">
    <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/page-titles/1.jpg') }" alt="background" />
  </div>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-12 col-md-12 col-lg-12 col-xl-8 text-center align-items-center d-flex flex-col">
        <img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/logo/niogg-logo-light.png')} alt="logo" class="max-w-28 mb-25"/>
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
    margin-top: 0 !important;
  }
</style>

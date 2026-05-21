<script>
  import { getImgUrl } from '@/helpers';
  export let appPhone = '';

  let aboutOpen = false;
  let newsOpen = false;

  function toggleAbout() {
    aboutOpen = !aboutOpen;
    newsOpen = false;
  }

  function toggleNews() {
    newsOpen = !newsOpen;
    aboutOpen = false;
  }
</script>

<header id="header" class="header header-transparent">
  <nav class="navbar navbar-expand-lg sticky-navbar">
    <div class="container">
      <a class="navbar-brand" href="/">
        <img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/logo/niogg-logo-light.png')} class="logo-light !max-w-16" alt="logo"/>
        <img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/logo/niogg-logo-dark.png')} class="logo-dark !max-w-16" alt="logo"/>
      </a>
      <button class="navbar-toggler" type="button">
        <span class="menu-lines"><span></span></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavigation">
        <ul class="navbar-nav ml-auto">
          <li class="nav__item">
            <a href="{ window.route('app.index') }" class="nav__item-link active">Home</a>
          </li>
          <li class="nav__item with-dropdown" class:opened={aboutOpen}>
            <a href="javascript:void(0)" class="dropdown-toggle nav__item-link !flex items-center justify-between pr-2" on:click|preventDefault={toggleAbout}>
              <span>About Us</span>
              <i class="fa fa-angle-right" on:click|preventDefault={toggleAbout}></i>
            </a>
            <ul class="dropdown-menu" class:show={aboutOpen}>
              <li class="nav__item"><a href="{ window.route('app.about') }" class="nav__item-link">Who We Are</a></li>
              <li class="nav__item"><a href="{ window.route('app.vision-and-values') }" class="nav__item-link">Vision &amp; Values</a></li>
              <li class="nav__item"><a href="{ window.route('app.careers') }" class="nav__item-link">Career</a></li>
            </ul>
          </li>
          <li class="nav__item with-dropdown" class:opened={newsOpen}>
            <a href="javascript:void(0)" class="dropdown-toggle nav__item-link !flex items-center justify-between pr-2" on:click|preventDefault={toggleNews}>
              <span>News & Media</span>
              <i class="fa fa-angle-right" on:click|preventDefault={toggleNews}></i>
            </a>
            <ul class="dropdown-menu" class:show={newsOpen}>
              <li class="nav__item"><a href="{ window.route('events.media-showcase') }" class="nav__item-link">Media Showcase</a></li>
              <li class="nav__item"><a href="{ window.route('app.gallery') }" class="nav__item-link">Gallery</a></li>
              <li class="nav__item"><a href="{ window.route('app.awards') }" class="nav__item-link">Awards and Recognitions</a></li>
              <li class="nav__item"><a href="{ window.route('app.blog.index') }" class="nav__item-link">News & Articles</a></li>
            </ul>
          </li>
          <li class="nav__item">
            <a href="{ window.route('app.contact') }" class="nav__item-link">Contact Us</a>
          </li>
        </ul>
      </div>
      <div class="navbar-modules ml-5">
        <ul class="list-unstyled d-flex align-items-center modules__btns-list">
          <li><a href="#" class="module__btn module__btn-search"><i class="fa fa-search"></i></a></li>
          <li class="d-none d-lg-block">
            <div class="module__btn module__btn-phone d-flex align-items-center">
              <i class="icon-phone mr-3"></i>
              <div>
                <span class="d-block">Call Us:</span>
                <a href="tel:55-654-541-17">{appPhone}</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

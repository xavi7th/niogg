<script>
  import { getImgUrl } from '@/helpers';
</script>

<section id="clients" class="clients clients-1 border-top">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="heading text-center mb-50">
          <span class="heading__subtitle">Partner with Us</span>
          <h2 class="heading__title">Our Partners</h2>
          <div class="divider__line divider__theme divider__center mb-0"></div>
        </div>

        <div class="carousel owl-carousel" data-slide="6" data-slide-md="4" data-slide-sm="2" data-autoplay="true"
          data-nav="false" data-dots="false" data-space="20" data-loop="true" data-speed="700">
          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/nnpc.jpg')} alt="partner thumb"/></a>
          </div>

          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/12.png')} alt="partner thumb"/></a>
          </div>

          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/icpc.jpeg')} alt="partner thumb"/></a>
          </div>

          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/13.png')} alt="partner thumb"/></a>
          </div>

          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/education-ministry.png')} alt="partner thumb"/></a>
          </div>

          <div class="client">
            <a href="#"><img src={getImgUrl('Modules/PublicPage/resources/template/assets/images/clients/11.png')} alt="partner thumb"/></a>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

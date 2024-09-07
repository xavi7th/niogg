<script>

  import { getImgUrl } from '@/helpers';

  export let appAltName = ''
</script>

<section id="servicesLayout3" class="services services-layout3 pt-120 pb-120 bg-gray">
  <div class="container">
    <div class="row heading mb-40">
      <div class="col-sm-12 col-md-6 col-lg-6">
        <span class="heading__subtitle">What We Do</span>
        <h2 class="heading__title">promoting good governance one state at a time</h2>
      </div>
      <div class="col-sm-12 col-md-6 col-lg-6">
        <p class="heading__desc">{appAltName} promotes good governance in Nigeria by empowering youth through leadership training,
          advocating for transparency and accountability in public institutions, enhancing the electoral system,
          and educating citizens on their roles in governance to encourage active participation and societal development.</p>
        <a href="#" class="btn btn__primary btn__lg mt-30">Join us Today</a>
      </div>
    </div>
  </div>
  <div class="container col-padding-0">
    <div class="row service-items-wrap">
      <div class="d-flex col-sm-6 col-md-6 col-lg-3">
        <div class="service-item">
          <div class="service__content">
            <!-- <div class="service__icon"><i class="icon-tank-1"></i></div> -->
            <h4 class="service__title">Independent Judiciary</h4>
            <p class="service__desc">
              We promote good governance by advocating for judicial independence and supporting anti-corruption efforts. We also guide citizens in developing a practical and informed political culture.
            </p>
            <a href="#" class="btn btn__secondary btn__link">
              <span>Read More</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/judiciary.webp?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
            <div class="service__overlay"><a href="{ getImgUrl('Modules/PublicPage/resources/images/src/services/judiciary.webp') }" class="zoom__icon"></a></div>
          </div>
        </div>
      </div>
      <div class="d-flex col-sm-6 col-md-6 col-lg-3">
        <div class="service-item">
          <div class="service__content">
            <!-- <div class="service__icon"><i class="icon-factory-1"></i></div> -->
            <h4 class="service__title">Youth Involvement</h4>
            <p class="service__desc">
              We engage in youth and leadership development at all levels, offering training and programs to empower Nigerian youth in governance and educate citizens to actively participate in societal development.
            </p>
            <a href="#" class="btn btn__secondary btn__link">
              <span>Read More</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/youth.png?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
            <div class="service__overlay"><a href="#" class="zoom__icon"></a></div>
          </div>
        </div>
      </div>
      <div class="d-flex col-sm-6 col-md-6 col-lg-3">
        <div class="service-item">
          <div class="service__content">
            <!-- <div class="service__icon"><i class="icon-gears"></i></div> -->
            <h4 class="service__title">Poverty Eradication</h4>
            <p class="service__desc">
              We are committed to eradicating poverty through targeted initiatives that address economic inequality. Our programs offer skills training, support economic empowerment, and create opportunities for marginalized communities.
            </p>
            <a href="#" class="btn btn__secondary btn__link">
              <span>Read More</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/poverty-eradication.jpg?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
            <div class="service__overlay"><a href="#" class="zoom__icon"></a></div>
          </div>
        </div>
      </div>
      <div class="d-flex col-sm-6 col-md-6 col-lg-3">
        <div class="service-item">
          <div class="service__content">
            <!-- <div class="service__icon"><i class="icon-eco"></i></div> -->
            <h4 class="service__title">Resource Management</h4>
            <p class="service__desc">
              We are dedicated to effective natural resources management by promoting sustainable practices and transparent policies. Our efforts aim to ensure responsible stewardship and that resources benefit local communities.
            </p>
            <a href="#" class="btn btn__secondary btn__link">
              <span>Read More</span>
              <i class="icon-arrow-right2"></i>
            </a>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/resources.jpg?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
            <div class="service__overlay"><a href="#" class="zoom__icon"></a></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  .service-item{
    display: flex;
    flex-direction: column;

    & :first-child{
      flex: 1;
    }
  }
</style>

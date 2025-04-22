<script>

  import { getImgUrl } from '@/helpers';

  export let appAltName = ''
</script>

<section id="servicesLayout3" class="services services-layout3 pt-120 pb-120 bg-gray">
  <div class="container">
    <div class="row heading mb-40">
      <div class="col-sm-12 col-md-6 col-lg-6">
        <span class="heading__subtitle">What We Do</span>
        <h2 class="heading__title">Beyond youth engagement</h2>
      </div>
      <div class="col-sm-12 col-md-6 col-lg-6">
        <p class="heading__desc">{appAltName} plays a pivotal role in championing transparency and accountability within public service, ensuring that leaders are held responsible for their actions. The organization also advocates for an effective and credible electoral system that reflects the true will of the people, fostering trust in Nigeria’s democratic institutions. Through robust citizen education, {appAltName} empowers individuals to understand their civic duties and uphold the Nigerian Constitution as a foundation for good governance.</p>
        <p class="heading__desc">Through its wide-reaching programs and campaigns, {appAltName} continues to inspire a new generation of leaders and reformers who are passionate about building a more inclusive and prosperous Nigeria.</p>
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
            <h4 class="service__title">Transparency and Accountability</h4>
            <p class="service__desc">
              We promote good governance by advocating for transparency in government processes and ensuring public access to critical information. We also hold leaders accountable by encouraging active citizen oversight and institutional integrity.
            </p>
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
            <h4 class="service__title">Credible Electoral Systems</h4>
            <p class="service__desc">
              We promote good governance by advocating for free, fair, and transparent elections that truly reflect the will of the people. We also support electoral reforms and civic engagement to strengthen public confidence in the democratic process.
            </p>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/inec.png?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
            <div class="service__overlay"><a href="#" class="zoom__icon"></a></div>
          </div>
        </div>
      </div>
      <div class="d-flex col-sm-6 col-md-6 col-lg-3">
        <div class="service-item">
          <div class="service__content">
            <!-- <div class="service__icon"><i class="icon-gears"></i></div> -->
            <h4 class="service__title">Citizen Education </h4>
            <p class="service__desc">
              We promote good governance by investing in citizen education that fosters awareness of democratic rights and responsibilities. We also equip individuals with the knowledge and tools needed to actively participate in governance and demand accountability from public officials.
            </p>
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
            <!-- <div class="service__icon"><i class="icon-eco"></i></div> -->
            <h4 class="service__title">Nigerian Constitution</h4>
            <p class="service__desc">
              We promote good governance by advocating for strict adherence to the Nigerian Constitution as the supreme guiding framework for leadership and law. We also educate citizens and institutions on constitutional rights and duties to ensure justice, equity, and the rule of law prevail.
            </p>
          </div>
          <div class="service__img">
            <enhanced:img src="@publicpage-assets/images/src/services/constitution.webp?h=250;300&w=300;450&blur=2&fit=cover" class="img-fluid" alt="service"/>
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

<script>
  import { getImgUrl } from '@/helpers';

  export let appAltName = '';
</script>

<section id="banner3" class="banner banner-3 p-0">
  <div class="container-fluid col-padding-0">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-6 bg-theme">
        <div class="inner-padding">
          <div class="heading heading-2 heading-white mb-30">
            <h2 class="heading__title">What is {appAltName}'s Vision?</h2>
            <p class="heading__desc">
              Our vision is for a Nigeria where empowered citizens actively participate in governance, where governments are transparent, accountable, and effective,
              where businesses thrive in a fair and just environment, and where poverty is eradicated. We aspire for {appAltName} to be recognized as the foremost
              partner in advancing and sustaining good governance across the nation.
            </p>

            <h2 class="heading__title">What is {appAltName}'s Mission?</h2>
            <p class="heading__desc">
              At {appAltName}, our mission is to transform Nigerian society by promoting exceptional governance. We are committed to empowering individuals, especially those
              impacted by poverty, unemployment, and poor governance, to hold their leaders accountable and take charge of improving their own lives. Our approach includes
              conducting rigorous policy research, advocating for effective governance, and building strategic partnerships to uplift societal and economic conditions.
              We also support government leaders and public servants by providing well-researched policy solutions aimed at fostering sustainable improvements in the
              lives of citizens. Through our efforts, we strive to cultivate a culture of accountability and prosperity, ensuring that every Nigerian can thrive in a
              just and equitable society.
            </p>

            <h2 class="heading__title">What are {appAltName}'s Values?</h2>
            <p class="heading__desc">
              {appAltName}'s core values—Transparency, Respect, Accountability, and Purpose—guide everything we do. Our independence ensures an apolitical stance and commitment
              to evidence-based research, while incisiveness drives impactful analysis. Integrity is the foundation of the trust we build, and courage empowers us to
              speak truth to power responsibly. Credibility demands that we maintain the highest standards in all our work. These values are embedded in our operations,
              upheld by our team, and are essential in establishing {appAltName} as a leader in promoting effective governance across Nigeria.
            </p>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-12 col-lg-6 bg-overlay bg-overlay-4 pt-100 pb-100">
        <div class="bg-img">
          <img src="{ getImgUrl('Modules/PublicPage/resources/template/assets/images/banners/good-governance.jpg') }" alt="background" />
        </div>
      </div>
    </div>
  </div>
</section>

<style lang="scss">
  #banner3{

    .heading__title{
      font-size: 1.8rem;
      margin-bottom: 5px;

      &:not(:first-child){
        margin-top: 20px;
      }
    }

    .heading__desc{
      font-size: 16px;
    }

    .inner-padding{
      padding: 50px;
    }
  }
</style>

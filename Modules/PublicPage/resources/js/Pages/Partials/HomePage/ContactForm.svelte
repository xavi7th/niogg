<script>
  import { getImgUrl } from '@/helpers';

  export let appPhone, appAltName;
</script>

<section id="requestQuote" class="request-quote-2 pt-0 pb-30">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="request-quote-panel">
          <div class="request__panel-banner bg-overlay bg-overlay-theme">
            <div class="bg-img">
              <img src="{ getImgUrl('Modules/PublicPage/resources/images/src/placeholders/2.jpg') }" alt="banner" />
            </div>
            <div class="request__panel-banner-inner">
              <h5 class="request__panel-title">We are looking for Heroes</h5>
              <p class="request__panel-desc">
                Are you passionate about making a difference in Nigeria? {appAltName} is looking for dedicated heroes to join our mission for good governance and community
                development. By becoming a member, you’ll contribute to our efforts in leadership training, poverty eradication, natural resources management,
                and more. Together, we can drive positive change and ensure a brighter future for all Nigerians. Reach out today and be a part of our transformative journey!
              </p>
            </div>
          </div>
          <div class="request__form mb-0">
            <div class="request__form-body">
              <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                  <h4 class="request__form-title">Join us Today</h4>
                  <p class="request__form-desc">Fill the form below and one of our reps will get in touch with you.</p>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name">
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email">
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Phone">
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                  <div class="form-group form-group-select">
                    <select class="form-control">
                      <option>How did you hear about us?</option>
                      <option>Facebook</option>
                      <option>Instagram</option>
                      <option>Twitter</option>
                      <option>LinkedIn</option>
                      <option>A Friend</option>
                      <option>Our Outreach Programme</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                  <div class="form-group">
                    <textarea class="form-control" placeholder="Additional Details!"></textarea>
                  </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12">
                  <button class="btn btn__secondary">Submit Request</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12 text-center mt-40">
        <p class="text__link mb-0">We will get back to you within 24 hours, or call us on
          <span class="font-bold color-theme">{appPhone}</span></p>
      </div>
    </div>
  </div>
</section>

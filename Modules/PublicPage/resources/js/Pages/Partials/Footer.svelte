<script>
  export let app;
</script>

<footer id="footer" class="footer footer-1">
  <div class="footer-top">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-4 footer__widget footer__widget-about  mb-30">
          <div class="footer__widget-content">
            <enhanced:img src="@publicpage-template/images/logo/niogg-logo-footer.png?enhanced&w=100" alt="logo"/>
            <p>{@html app.address}</p>
            <p>Mail: <a href="mailto:{app.email}" class="font-weight-bold">{app.email}</a></p>
            <p>Mail: <a href="mailto:{app.alt_email}" class="font-weight-bold">{app.alt_email}</a></p>
            <p>Phone: <a href="tel:{app.phone}" class="font-weight-bold">{app.phone}</a></p>
            <p>Phone: <a href="tel:{app.alt_phone}" class="font-weight-bold">{app.alt_phone}</a></p>
          </div>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-2 offset-2 footer__widget footer__widget-nav pt-24">
          <h6 class="footer__widget-title">Company</h6>
          <div class="footer__widget-content">
            <nav>
              <ul class="list-unstyled">
                <li><a href="{ window.route('app.about') }">About Us</a></li>
                <li><a href="{ window.route('app.about') }#team">Meet Our Team</a></li>
                <li><a href="#/">News & Media</a></li>
                <li><a href="#/">Case Studies</a></li>
                <li><a href="{ window.route('app.contact') }">Contacts</a></li>
              </ul>
            </nav>
          </div>
        </div>
        <!-- <div class="col-6 col-sm-6 col-md-3 col-lg-2 footer__widget footer__widget-nav">
          <h6 class="footer__widget-title">Industries</h6>
          <div class="footer__widget-content">
            <nav>
              <ul class="list-unstyled">
                <li><a href="#">Retail & Consumer</a></li>
                <li><a href="#">Sciences & Healthcare</a></li>
                <li><a href="#">Industrial & Chemical</a></li>
                <li><a href="#">Power Generation</a></li>
                <li><a href="#">Food & Beverage</a></li>
                <li><a href="#">Oil & Gas</a></li>
              </ul>
            </nav>
          </div>
        </div> -->
        <!-- <div class="col-12 col-sm-12 col-md-6 col-lg-4 footer__widget footer__widget-newsletter">
          <div class="footer__widget-content">
            <h6 class="footer__widget-title">Newsletter</h6>
            <p>Sign up for industry alerts, our latest news, thoughts, and insights from Industic.</p>
            <form class="widget__newsletter-form">
              <div class="form-group mb-0">
                <input type="text" class="form-control" placeholder="Your Email Address ">
                <button type="submit" class="btn btn__primary btn__hover2">Subscribe</button>
              </div>
            </form>
          </div>
          <div class="text-right mt-20">
            <span class="footer__contact-link">Have a question?
              <a href="contacs.html">Click here</a>
            </span>
          </div>
        </div> -->
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-9 col-lg-9">
          <div class="footer__copyright">
            <nav>
              <ul class="list-unstyled footer__copyright-links d-flex flex-wrap">
                <li>
                  <a href="tel:+2348034411661">
                    &copy; {new Date().getFullYear()} Minds &amp; Machines, with Love by <span class="color-theme">+234 803 441 1661</span>
                  </a>
                </li>
                <li><a href="#">Terms & Conditions </a></li>
                <li><a href="#">Privacy Policy</a></li>
              </ul>
            </nav>

          </div>
        </div>
        <div class="col-sm-12 col-md-3 col-lg-3 d-flex align-items-center">
          <div class="social__icons justify-content-end w-100">
            <a href="https://www.facebook.com/profile.php?id=61563097853769&mibextid=ZbWKwL" target="_blank" referrerpolicy="origin"><i class="fa fa-facebook"></i></a>
            <a href="https://x.com/InsightNig40403?t=idIVOXJgeRXutGyBLTfcSg&s=09" target="_blank" referrerpolicy="origin"><i class="fa fa-twitter"></i></a>
            <a href="https://www.instagram.com/nigeriainsight/" target="_blank" referrerpolicy="origin"><i class="fa fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

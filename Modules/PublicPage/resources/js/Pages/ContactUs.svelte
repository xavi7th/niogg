<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;

</script>

<script>
  import { page, router } from "@inertiajs/svelte";
  import PageTitle from '@publicpage-partials/PageTitle.svelte';

  $: ({ app } = $page.props);

  let details = {}, msgSending = false;

  let contactUs = () => {
    msgSending = true
    console.log(details);

    router.post(window.route('app.contact.store'), details, {
      onSuccess: () => details = {},
      onFinish: () => msgSending = false,
      preserveScroll: true,
      preserveState: true,
    });
  }
</script>

<PageTitle appName={app.name} pageTitle='Contact Us'>
  <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
</PageTitle>

<section id="googleMap" class="google-map p-0 mt-[-6rem]">
  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31768.9201481188!2d5.743550435574028!3d5.5499620435399875!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1041adbd1247fad1%3A0x6c9a0a01169d37c!2sUnity%20Bank!5e0!3m2!1sen!2sng!4v1724303408961!5m2!1sen!2sng"
        width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>


<section id="contact1" class="contact pt-120 pb-110 md:pt-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-8">
        <div class="heading text-center mb-50">
          <span class="heading__subtitle">We want to hear from you</span>
          <h2 class="heading__title">Reach out to us</h2>
          <div class="divider__line divider__theme divider__center mb-0"></div>
          <p class="heading__desc">
            Thank you for your interest in joining us to empower the Nigerian people, enhance government effectiveness, and promoting effective governance. By connecting with us,
            you become a vital part of our mission to advance good governance and make a meaningful impact. Please fill out the form below to get involved and contribute to our vision
            for a better Nigaria.
          </p>
        </div>
      </div>

      <div class="col-sm-10 col-md-12 col-lg-5">
        <div class="contact-panel mb-40">
          <h6 class="contact__panel-title">Contact Details</h6>
          <ul class="contact__panel-list list-unstyled">
            <li><i class="fa fa-map-marker"></i><span class="font-weight-bold">{@html app.address}</span></li>
            <li><i class="fa fa-envelope"></i><span class="font-weight-bold">Email: {app.email}</span></li>
            <li><i class="fa fa-envelope"></i><span class="font-weight-bold">Email: {app.alt_email}</span></li>
            <li><i class="fa fa-phone"></i><span class="font-weight-bold">Phone: {app.phone}</span></li>
            <li><i class="fa fa-phone"></i><span class="font-weight-bold">Phone: {app.alt_phone}</span></li>
          </ul>
        </div>

        <div class="contact-panel mb-40">
          <h6 class="contact__panel-title">Opening Hours</h6>
          <ul class="contact__panel-list contact__panel-list-2 list-unstyled">
            <li><span>Monday-Friday</span><span class="font-weight-bold mr-32 xl:mr-42">{app.working_hours}</span></li>
            <li><span>Saturday</span><span class="font-weight-bold mr-32 xl:mr-42">{app.working_hours}</span></li>
            <li><span>Sunday</span><span class="font-weight-bold mr-32 xl:mr-42">Closed</span></li>
          </ul>
        </div>
      </div>

      <div class="col-sm-12 col-md-12 col-lg-7">
        <form on:submit|preventDefault|stopPropagation={contactUs} class="mt-11">
          <div class="row">
            <div class="col-sm-4 col-md-4 col-lg-4">
              <div class="form-group"><input required type="text" class="form-control" placeholder="Name" bind:value={details.name}></div>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-4">
              <div class="form-group"><input required type="email" class="form-control" placeholder="Email" bind:value={details.email}></div>
            </div>
            <div class="col-sm-4 col-md-4 col-lg-4">
              <div class="form-group"><input required type="text" class="form-control" placeholder="Phone" bind:value={details.phone}></div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
              <div class="form-group"><textarea required class="form-control" placeholder="Message" bind:value={details.message}></textarea></div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-12">
              <button type="submit" class="btn btn__secondary btn__hover3 btn__block" disabled={msgSending}>Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

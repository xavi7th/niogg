<script context="module">
  import PublicPageLayout from "@publicpage-pages/Layouts/PublicPageLayout.svelte";
  export const layout = PublicPageLayout;
</script>

<script>
  import { router } from "@inertiajs/svelte";
  import { page } from "@inertiajs/svelte";
  import * as Dialog from "@/Components/ui/dialog";
  import { Separator } from "@/Components/ui/separator";


  $: ({ app } = $page.props);

  /** @type {import('@root/types').ConferenceRegistrant} */
  let details = {};
  let isLoading = false;

  let processRegistration = regType => {
    isLoading = true;

    details.conference_tag = 'launch_conference';
    details.is_manual_reg = regType == 'manual'

    router.post(route('app.conferences.launch.store'), details, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => details = {},
      onFinish: () => isLoading = false,
    })
  }
</script>

<div class="row relative bg-gray-100 px-2 py-16 !mb-20">
  <!-- <div class="col-sm-12 col-lg-4 md:flex lg:flex-col">
    <div class="contact-panel mb-40 w-full md:w-2/5 lg:w-full">
      <h6 class="contact__panel-title">Bank Details for Manual Registration</h6>
      <ul class="contact__panel-list list-unstyled">
        <li class="capitalize"><i class="fa fa-bank"></i><span>2045475254</span></li>
        <li class="capitalize"><i class="fa fa-user"></i><span>Nigeria insight on good governance</span></li>
        <li class="capitalize"><i class="fa fa-bank"></i><span>First Bank Nigeria</span></li>
      </ul>
    </div>
    <div class="contact-panel md:mt-6 lg:mt-0 mb-40 w-full md:w-3/5 lg:w-full">
      <ul class="contact__panel-list contact__panel-list-2 list-unstyled">
        <li>
          <span class="text__link">
            Send us your payment receipt on <span class="font-bold color-theme">{app.phone}</span> and we will get back to you within 24 hours
            with your event registration details or you can register automatically using the form<span class="lg:hidden">&nbsp; below</span>.
          </span>
        </li>
      </ul>
    </div>
  </div> -->

  <div class="col-sm-12 col-lg-10 mx-auto" id="become-a-sponsor">
    <div class="request-quote-panel">
      <div class="request__form mb-0 !basis-full">
        <div class="request__form-body">
          <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
              <h4 class="request__form-title">Book Your Reservation</h4>
              <p class="request__form-desc">Fill the form below and follow the steps to complete your registration.</p>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <input name="full_name"  type="text" class="form-control" placeholder="Name" bind:value={details.full_name}>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <input name="email"  type="email" class="form-control" placeholder="We will send your registration details via mail to you" bind:value={details.email}>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <input  name="phone" type="text" class="form-control" placeholder="Phone (preferrably your WhatsApp number)" bind:value={details.phone}>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <input  name="amount" type="number" class="form-control" placeholder="Amount" bind:value={details.amount}>
              </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12 text-right">
              <Dialog.Root>
                <Dialog.Trigger class="btn btn__secondary" disabled={!details.email || !details.amount}><span>Register Now</span></Dialog.Trigger>
                <Dialog.Content class="max-w-3xl">
                  <Dialog.Header>
                    <!-- <Dialog.Title class="text-4xl">Choose your Reservation type</Dialog.Title> -->
                    <Dialog.Description>
                      <div class="relative isolate px-6">
                        <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                          <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#a18802] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                        </div>

                        <div class="mx-auto z-30">
                          <div class="text-center">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manual Registration</h1>
                            <p class="mt-6 text-lg leading-8 text-gray-600">
                              <span class="text__link">
                                Register for the conference by paying into our bank account. You can use a transfer or you can go to any First Bank close to you to make payments.
                              </span>
                            </p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                              <Dialog.Root closeOnEscape={false} onOutsideClick={() => processRegistration('manual')}>
                                <Dialog.Trigger>
                                  <span class="text-sm font-semibold leading-6 text-gray-900">Register Manually <span aria-hidden="true">→</span></span>
                                </Dialog.Trigger>
                                <Dialog.Content class="max-w-2xl">
                                  <Dialog.Header>
                                    <Dialog.Title class="text-3xl font-semibold text-gray-700">Manual Registration <span class="text-yellow-700">Procedure</span></Dialog.Title>
                                    <Dialog.Description>
                                      <div class="flex bg-white">
                                        <div class="flex items-center px-8 md:px-12">
                                          <div>
                                            <p class="mt-2 text-sm text-gray-500 md:text-base text-justify">
                                              <span class="text__link">
                                                Make your payment into the account below. After payment has been made, send us your payment receipt to this number on WhatsApp <span class="font-bold color-theme">{app.phone}</span> or to
                                                our email address at <span class="font-bold color-theme">{app.email}</span>.
                                                <br>
                                                <br>
                                                Within 6 hours, your event registration details will be sent to you via WhatsApp and email.
                                              </span>
                                            </p>
                                            <div class="contact-panel mb-40 w-full md:w-2/5 lg:w-full text-left">
                                              <h6 class="contact__panel-title text-xl">Bank Details</h6>
                                              <ul class="contact__panel-list list-unstyled">
                                                <li class="capitalize text-black text-lg"><i class="fa fa-bank"></i><span>2045475254</span></li>
                                                <li class="capitalize text-black text-lg"><i class="fa fa-user"></i><span>Nigeria insight on good governance</span></li>
                                                <li class="capitalize text-black text-lg"><i class="fa fa-bank"></i><span>First Bank Nigeria</span></li>
                                              </ul>
                                            </div>
                                            <div class="flex justify-center mt-6">
                                              <Dialog.Close
                                                  class="btn btn__primary shadow-sm !w-min px-4 text-nowrap focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark focus-visible:ring-offset-2 focus-visible:ring-offset-background active:scale-98"
                                                  on:click={() => processRegistration('manual')}
                                                >
                                                  I have copied the bank details
                                                </Dialog.Close>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </Dialog.Description>
                                  </Dialog.Header>
                                </Dialog.Content>
                              </Dialog.Root>

                            </div>
                          </div>
                        </div>

                        <!-- <Separator class="my-8 bg-slate-200 h-0.5" />

                        <div class="mx-auto z-30 pb-6">
                          <div class="text-center">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Automatic Registration</h1>
                            <p class="mt-6 text-lg leading-8 text-gray-600">
                              <span class="text__link">
                                Register for the conference by paying into our bank account. You can use a transfer or you can go to any First Bank close to you to make payments.
                              </span>
                            </p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                              <button  on:click|preventDefault|stopPropagation={processRegistration} class="btn btn__secondary shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600">
                                Register Now
                              </button>
                            </div>
                          </div>
                        </div> -->

                        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                          <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#a18802] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                        </div>
                      </div>
                    </Dialog.Description>
                  </Dialog.Header>
                </Dialog.Content>
              </Dialog.Root>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style lang="scss">
  #become-a-sponsor{
    scroll-margin-top: 150px;

    @media (max-width: 991px) {
      scroll-margin-top: 250px;
    }

    @media (max-width: 540px) {
      scroll-margin-top: 400px;
    }
  }
</style>

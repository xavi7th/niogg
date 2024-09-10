import '@publicpage-assets/sass/app.scss'
import { createInertiaApp } from '@inertiajs/svelte';

import { router } from "@inertiajs/svelte";
import { getErrorString } from "@/helpers";

import Swal from 'sweetalert2'

window.swl = Swal;

window.Toast = swl.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  showCloseButton: true,
  allowEscapeKey: true,
  timer: 2000,
  icon: "success",
  didOpen: ( toast ) => {
    toast.addEventListener('mouseenter', swl.stopTimer)
    toast.addEventListener('mouseleave', swl.resumeTimer)
  }
});

window.ToastLarge = swl.mixin({
  icon: "success",
  title: 'To be implemented!',
  html: 'I will close in <b></b> milliseconds.',
  timer: 10000,
  timerProgressBar: true,
  showConfirmButton: false,
  allowEscapeKey: true,
  willOpen: () => {
    swl.showLoading()
  },
  didOpen: ( toast ) => {
    toast.addEventListener('mouseenter', swl.stopTimer)
    toast.addEventListener('mouseleave', swl.resumeTimer)
  }

  // willClose: () => {}
})

window.BlockToast = swl.mixin({
  willOpen: () => {
    swl.showLoading()
  },
  showConfirmButton: false,
  showCloseButton: false,
  allowOutsideClick: false,
  allowEscapeKey: false
});

window.swlPreconfirm = swl.mixin({
  title: 'Are you sure?',
  text: "Implement this when you call the mixin",
  icon: 'question',
  showCloseButton: false,
  backdrop: true,
  allowOutsideClick: () => !swl.isLoading(),
  allowEscapeKey: false,
  showCancelButton: true,
  focusCancel: true,
  cancelButtonColor: '#d33',
  confirmButtonColor: '#3085d6',
  confirmButtonText: 'To be implemented',
  showLoaderOnConfirm: true,
  preConfirm: () => {
    /** Implement this when you call the mixin */
  },
})

router.on('start', ( event ) => { })

router.on('progress', ( event ) => {
  process.env.NODE_ENV === 'development' && console.log(event);
})

router.on('success', ( e ) => {
  if ( e.detail.page.props.flash.success ) {
    ToastLarge.fire({
      title: "Success",
      html: e.detail.page.props.flash.success,
      icon: "success",
      timer: 3000,
      allowEscapeKey: true
    });
  } else if ( e.detail.page.props.flash.warning ) {
    ToastLarge.fire({
      title: "Note!!",
      html: e.detail.page.props.flash.warning,
      icon: "warning",
      timer: 5000,
      allowEscapeKey: true
    });
  } else if ( e.detail.page.props.flash.info ) {
    ToastLarge.fire({
      title: "Note!!",
      html: e.detail.page.props.flash.info,
      icon: "info",
      timer: 7000,
      allowEscapeKey: true
    });
  } else if ( e.detail.page.props.flash.toast_info ) {
    Toast.fire({
      title: "",
      html: e.detail.page.props.flash.toast_info,
      icon: "info",
      timer: 7000,
      allowEscapeKey: true
    });
  } else {
    swl.close();
  }
})

router.on('error', ( e ) => {
  process.env.NODE_ENV === 'development' && console.log(`There were errors on your visit`)
  process.env.NODE_ENV === 'development' && console.log(e);

  console.log(getErrorString(e.detail.errors));

  ToastLarge.fire({
    title: "Error",
    html: getErrorString(e.detail.errors),
    icon: "error",
    timer: 10000, //milliseconds
    footer:
      `<span class="fw-light fs-7 lh-1">
        Are you having issues here? Contact us via our support email: &nbsp;&nbsp; <a target="_blank" rel="noopener noreferrer" href="mailto:contact@enski.net">contact@enski.net</a>
      </span>`,
  });
})

router.on('invalid', ( event ) => {
  process.env.NODE_ENV === 'development' && console.log(`An invalid router response was received.`, event)
  event.preventDefault();

  if ( event.detail.response.status === 403 ) {
    Toast.fire({ position: 'top', text: 'Action forbidden!', icon: 'error' })
  } else {
    Toast.fire({ position: 'top', title: 'Oops!', text: event.detail.response.statusText, icon: 'error' })
  }
})

router.on('exception', ( event ) => {
  process.env.NODE_ENV === 'development' && console.log(event);
  process.env.NODE_ENV === 'development' && console.log(`An unexpected error occurred during an Inertia visit.`)
  console.log(event.detail.error);
})

router.on('finish', ( e ) => {
  // console.log(e);
})

console.info(
    `%c${process.env.NODE_ENV?.toUpperCase()} MODE`,
    `
    color: white;
    border-radius: 0.125rem;
    padding: 0.25rem 0.5rem;
    background-color: ${process.env.NODE_ENV !== 'development' ? 'green' : '#E36049'}
    `
);


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob(['./Pages/**/*.svelte', '../../../../Modules/**/Pages/**/*.svelte'], { eager: true })

    let page = undefined, pageUrl = undefined;

    if ( name.includes('::') ) {
      let [module, pageLocation] = name.split('::');
      page = pages[pageUrl = `../../../${module}/resources/js/Pages/${pageLocation}.svelte`] ?? pages[pageUrl = `./Pages/${pageLocation}.svelte`];
    }

    if ( page == undefined ) {
      throw new Error(`Page not found: ${pageUrl}`);
    }

    return page
  },

  setup({ el, App, props }) {
    let pageProps = props.initialPage.props;

    if (Object.entries(pageProps.errors).length) {
      ToastLarge.fire({
          title: "Error",
          html: getErrorString(pageProps.errors),
          icon: "error",
          timer:10000, //milliseconds
          footer:
          `<span class="fw-light fs-7 lh-1">
            Are you having issues here? Contact us via our support email: &nbsp;&nbsp; <a target="_blank" rel="noopener noreferrer" href="mailto:contact@enski.net">contact@enski.net</a>
          </span>`,
      });
    } else if (pageProps.flash.warning) {
      ToastLarge.fire({
        title: "Note!!",
        html: pageProps.flash.warning,
        icon: "warning",
        timer: 15000,
        allowEscapeKey: true
      });
    } else if (pageProps.flash.success) {
      ToastLarge.fire({
        title: "Success",
        html: pageProps.flash.success,
        icon: "success",
        timer: 10000,
        allowEscapeKey: true
      });
    } else if ( pageProps.flash.toast_info ) {
      Toast.fire({
        title: "",
        html: pageProps.flash.toast_info,
        icon: "info",
        position: 'center',
        timer: 7000,
        allowEscapeKey: true
      });
    }

    new App({ target: el, props })
  },

  progress: {
    color: '#4B5563',
  },
})

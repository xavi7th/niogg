import { writable } from 'svelte/store';

export const pageTitle = writable('Enski Integrated');
export const pageDescription = writable('');

export const isMobileScreen = writable(window.matchMedia('(max-width: 1023.5px)').matches)

window.matchMedia('(min-width: 1023.5px)').addEventListener("change", () => {
  if ( window.matchMedia('(max-width: 1023.5px)').matches ) {
    isMobileScreen.update(n => true);
  } else {
    isMobileScreen.update(n => false);
  }
})

export const isOnline = writable(navigator.onLine);

let updateOnlineStatus = ( e ) => {
  const { type } = e;

  isOnline.update(n => type === 'online');

  if ( navigator.onLine ) {
    Toast.fire({
      html: 'Great! Network connection restored.',
      timer: 2000,
      icon: "success",
      position: 'center'
    })
  } else {
    BlockToast.fire({
      title: 'Oops',
      html: 'Network connection lost! Try checking the network cables, modem, and router, reconnecting to Wi-Fi or moving closer to your router',
      timer: 200000,
      icon: "warning",
    })
  }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

console.log('---====== network-status-checker activated =====---');

// EXAMPLE USABE
// import { pageHeader } from "@/stores";

import { writable } from "svelte/store";

export const modalRoot = writable(undefined);

export const pageTitle = writable("NIOGG");
export const pageHeader = writable("");
export const pageDescription = writable("");

export const isMobileScreen = writable(window.matchMedia("(max-width: 1023.5px)").matches);

window.matchMedia("(min-width: 1023.5px)").addEventListener("change", () => {
  if (window.matchMedia("(max-width: 1023.5px)").matches) {
    isMobileScreen.update((n) => true);
  } else {
    isMobileScreen.update((n) => false);
  }
});

export const isOnline = writable(navigator.onLine);

// Dark mode store with localStorage persistence
const getInitialTheme = () => {
	if (typeof window !== 'undefined') {
		const stored = localStorage.getItem('theme');
		if (stored) return stored === 'dark';
		return window.matchMedia('(prefers-color-scheme: dark)').matches;
	}
	return false;
};

export const darkMode = writable(getInitialTheme());

if (typeof window !== 'undefined') {
	darkMode.subscribe((value) => {
		if (value) {
			document.documentElement.classList.add('dark');
			localStorage.setItem('theme', 'dark');
		} else {
			document.documentElement.classList.remove('dark');
			localStorage.setItem('theme', 'light');
		}
	});
}

let updateOnlineStatus = (e) => {
  const { type } = e;

  isOnline.update((n) => type === "online");

  if (navigator.onLine) {
    Toast.fire({
      html: "Great! Network connection restored.",
      timer: 2000,
      icon: "success",
      position: "center",
    });
  } else {
    BlockToast.fire({
      title: "Oops",
      html: "Network connection lost! Try checking the network cables, modem, and router, reconnecting to Wi-Fi or moving closer to your router",
      timer: 200000,
      icon: "warning",
    });
  }
};

window.addEventListener("online", updateOnlineStatus);
window.addEventListener("offline", updateOnlineStatus);

console.log("---====== network-status-checker activated =====---");

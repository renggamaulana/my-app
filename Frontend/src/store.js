import { writable, derived } from "svelte/store";

// Authentication State
export const token = writable(localStorage.getItem("token") || null);
export const user = writable(null);

export const isLoggedIn = derived(token, ($token) => !!$token);

// Sync token changes to localStorage
token.subscribe((value) => {
  if (value) {
    localStorage.setItem("token", value);
  } else {
    localStorage.removeItem("token");
  }
});

// Categories and Filter State
export const categories = writable([]);
export const selectedCategory = writable(null); // Holds ID of selected category

// Search State
export const searchTerm = writable("");

// Cart State
export const cart = writable([]);

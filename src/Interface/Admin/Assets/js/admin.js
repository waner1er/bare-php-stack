// Import the SCSS
import "../scss/admin.scss";

// Import page-specific modules
import "./pages/menu.js";

// Admin JavaScript
console.log("Admin panel loaded");

// Example: CSRF token auto-insert
document.addEventListener("DOMContentLoaded", () => {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  if (csrfToken) {
    // Auto-add CSRF to all forms
    document.querySelectorAll("form").forEach((form) => {
      if (!form.querySelector('input[name="_token"]')) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "_token";
        input.value = csrfToken;
        form.appendChild(input);
      }
    });
  }
});

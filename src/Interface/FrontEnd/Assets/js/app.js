// Import the SCSS
import "../scss/app.scss";

// FrontEnd JavaScript
console.log("FrontEnd app loaded");

// Navigation mobile toggle
document.addEventListener("DOMContentLoaded", () => {
  const mobileMenuToggle = document.querySelector("[data-mobile-menu-toggle]");
  const mobileMenu = document.querySelector("[data-mobile-menu]");

  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener("click", () => {
      mobileMenu.classList.toggle("active");
    });
  }
});

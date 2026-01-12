// Import the SCSS
import "../scss/common.scss";

// Common JavaScript utilities
console.log("Common utilities loaded");

// Example: Alert auto-dismiss
export function initAlerts() {
  document.querySelectorAll("[data-alert-dismiss]").forEach((alert) => {
    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
}

// Example: Confirm dialogs
export function confirmAction(message = "Êtes-vous sûr ?") {
  return confirm(message);
}

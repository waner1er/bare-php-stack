/**
 * Menu Management Page
 * Handles form validation, slug checking, and dynamic form filling
 */

// Slugs réservés pour les pages système
const RESERVED_SLUGS = [
  "contact",
  "archive",
  "login",
  "register",
  "logout",
  "posts",
  "admin",
];

// Slugs déjà utilisés dans le menu (injecté depuis le serveur)
let usedMenuSlugs = [];

/**
 * Initialize menu page functionality
 */
export function initMenuPage() {
  // Check if we're on the menu page
  const menuForm = document.getElementById("menuForm");
  if (!menuForm) return;

  // Get used slugs from data attribute
  const slugsData = menuForm.dataset.usedSlugs;
  if (slugsData) {
    usedMenuSlugs = JSON.parse(slugsData);
  }

  // Setup event listeners
  setupEventListeners();
}

/**
 * Setup all event listeners for the menu page
 */
function setupEventListeners() {
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");
  const menuForm = document.getElementById("menuForm");

  if (slugInput) {
    slugInput.addEventListener("input", validateSlug);
  }

  if (typeInput) {
    typeInput.addEventListener("change", validateSlug);
  }

  if (menuForm) {
    menuForm.addEventListener("submit", handleFormSubmit);
  }
}

/**
 * Fill form with provided values
 * @param {string} label - The label for the menu item
 * @param {string} slug - The slug for the menu item
 * @param {string} type - The type of menu item (post/archive)
 */
export function fillForm(label, slug, type) {
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");

  if (labelInput) labelInput.value = label;
  if (slugInput) slugInput.value = slug;
  if (typeInput) typeInput.value = type;

  validateSlug();
}

/**
 * Validate the slug field
 */
function validateSlug() {
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");
  const errorEl = document.getElementById("slug-error");
  const warningEl = document.getElementById("slug-warning");
  const submitBtn = document.getElementById("submitBtn");

  if (!slugInput || !typeInput || !errorEl || !warningEl || !submitBtn) return;

  const slug = slugInput.value.trim();
  const type = typeInput.value;

  // Reset states
  errorEl.style.display = "none";
  warningEl.style.display = "none";
  submitBtn.disabled = false;

  if (!slug) return;

  // Check if slug is already in menu
  if (usedMenuSlugs.includes(slug)) {
    errorEl.textContent = "⚠️ Ce slug est déjà dans le menu";
    errorEl.style.display = "block";
    submitBtn.disabled = true;
    return;
  }

  // Check if slug is reserved (except for post types)
  if (type !== "post" && RESERVED_SLUGS.includes(slug)) {
    errorEl.textContent = "⚠️ Ce slug est réservé pour une page système";
    errorEl.style.display = "block";
    submitBtn.disabled = true;
    return;
  }

  // Warning if reserved slug but post type
  if (type === "post" && RESERVED_SLUGS.includes(slug)) {
    warningEl.textContent =
      "⚠️ Attention : ce slug correspond à une page système, il y aura un conflit";
    warningEl.style.display = "block";
  }
}

/**
 * Handle form submission
 * @param {Event} e - The submit event
 */
function handleFormSubmit(e) {
  const slugInput = document.getElementById("slug");
  if (!slugInput) return;

  const slug = slugInput.value.trim();

  if (usedMenuSlugs.includes(slug)) {
    e.preventDefault();
    alert("Ce slug est déjà dans le menu");
    return false;
  }
}

/**
 * Toggle category select based on type
 */
export function toggleCategorySelect() {
  const type = document.getElementById("type");
  const categoryGroup = document.getElementById("categoryGroup");
  const postsSection = document.getElementById("postsSection");
  const categoriesSection = document.getElementById("categoriesSection");
  const categoryIdInput = document.getElementById("category_id");

  if (!type) return;

  if (type.value === "archive") {
    if (categoryGroup) categoryGroup.style.display = "block";
    if (postsSection) postsSection.style.display = "none";
    if (categoriesSection) categoriesSection.style.display = "block";
  } else {
    if (categoryGroup) categoryGroup.style.display = "none";
    if (postsSection) postsSection.style.display = "block";
    if (categoriesSection) categoriesSection.style.display = "none";
    if (categoryIdInput) categoryIdInput.value = "";
  }
}

/**
 * Fill form for all posts archive
 */
export function fillFormForAllPosts() {
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");
  const categoryIdInput = document.getElementById("category_id");

  if (labelInput) labelInput.value = "Tous les articles";
  if (slugInput) slugInput.value = "posts";
  if (typeInput) typeInput.value = "archive";
  if (categoryIdInput) categoryIdInput.value = "";

  toggleCategorySelect();
  validateSlug();
}

/**
 * Fill form for specific category
 * @param {string} name - Category name
 * @param {string} slug - Category slug
 * @param {number} categoryId - Category ID
 */
export function fillFormForCategory(name, slug, categoryId) {
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");
  const categoryIdInput = document.getElementById("category_id");

  if (labelInput) labelInput.value = name;
  if (slugInput) slugInput.value = slug;
  if (typeInput) typeInput.value = "archive";
  if (categoryIdInput) categoryIdInput.value = categoryId;

  toggleCategorySelect();
  validateSlug();
}

/**
 * Update label and slug when category is selected
 */
export function updateLabelAndSlugFromCategory() {
  const categorySelect = document.getElementById("category_id");
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");

  if (!categorySelect || !labelInput || !slugInput) return;

  const selectedOption = categorySelect.options[categorySelect.selectedIndex];

  if (selectedOption.value === "") {
    // All categories
    labelInput.value = "Tous les articles";
    slugInput.value = "posts";
  } else {
    // Specific category - use category slug
    const categoryName = selectedOption.getAttribute("data-name");
    const categorySlug = selectedOption.getAttribute("data-slug");
    labelInput.value = categoryName;
    slugInput.value = categorySlug;
  }

  // Trigger validation
  validateSlug();
}

// Auto-initialize when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initMenuPage);
} else {
  initMenuPage();
}

// Export functions to window for inline onclick handlers (temporary until we refactor to data attributes)
window.fillForm = fillForm;
window.toggleCategorySelect = toggleCategorySelect;
window.fillFormForAllPosts = fillFormForAllPosts;
window.fillFormForCategory = fillFormForCategory;
window.updateLabelAndSlugFromCategory = updateLabelAndSlugFromCategory;

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

export function initMenuPage() {
  const menuForm = document.getElementById("menuForm");
  if (!menuForm) return;

  const slugsData = menuForm.dataset.usedSlugs;
  if (slugsData) {
    usedMenuSlugs = JSON.parse(slugsData);
  }

  setupEventListeners();
}

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

export function fillForm(label, slug, type) {
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");

  if (labelInput) labelInput.value = label;
  if (slugInput) slugInput.value = slug;
  if (typeInput) typeInput.value = type;

  validateSlug();
}

function validateSlug() {
  const slugInput = document.getElementById("slug");
  const typeInput = document.getElementById("type");
  const errorEl = document.getElementById("slug-error");
  const warningEl = document.getElementById("slug-warning");
  const submitBtn = document.getElementById("submitBtn");

  if (!slugInput || !typeInput || !errorEl || !warningEl || !submitBtn) return;

  const slug = slugInput.value.trim();
  const type = typeInput.value;

  errorEl.style.display = "none";
  warningEl.style.display = "none";
  submitBtn.disabled = false;

  if (!slug) return;

  if (usedMenuSlugs.includes(slug)) {
    errorEl.textContent = "Ce slug est déjà dans le menu";
    errorEl.style.display = "block";
    submitBtn.disabled = true;
    return;
  }

  if (type !== "post" && RESERVED_SLUGS.includes(slug)) {
    errorEl.textContent = "Ce slug est réservé pour une page système";
    errorEl.style.display = "block";
    submitBtn.disabled = true;
    return;
  }

  if (type === "post" && RESERVED_SLUGS.includes(slug)) {
    warningEl.textContent =
      "Attention : ce slug correspond à une page système, il y aura un conflit";
    warningEl.style.display = "block";
  }
}

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

export function updateLabelAndSlugFromCategory() {
  const categorySelect = document.getElementById("category_id");
  const labelInput = document.getElementById("label");
  const slugInput = document.getElementById("slug");

  if (!categorySelect || !labelInput || !slugInput) return;

  const selectedOption = categorySelect.options[categorySelect.selectedIndex];

  if (selectedOption.value === "") {
    labelInput.value = "Tous les articles";
    slugInput.value = "posts";
  } else {
    const categoryName = selectedOption.getAttribute("data-name");
    const categorySlug = selectedOption.getAttribute("data-slug");
    labelInput.value = categoryName;
    slugInput.value = categorySlug;
  }

  validateSlug();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initMenuPage);
} else {
  initMenuPage();
}

// Appelées par les onclick de la vue menu (à passer en data-attributes)
window.fillForm = fillForm;
window.toggleCategorySelect = toggleCategorySelect;
window.fillFormForAllPosts = fillFormForAllPosts;
window.fillFormForCategory = fillFormForCategory;
window.updateLabelAndSlugFromCategory = updateLabelAndSlugFromCategory;

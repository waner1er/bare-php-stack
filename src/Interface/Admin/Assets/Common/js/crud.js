// JavaScript pour le gestionnaire CRUD

document.addEventListener("DOMContentLoaded", () => {
  initCrudTable();
  initCrudForm();
});

/**
 * Initialise les fonctionnalités du tableau CRUD
 */
function initCrudTable() {
  const sortableHeaders = document.querySelectorAll(".crud-sortable");

  sortableHeaders.forEach((header) => {
    header.addEventListener("click", () => {
      const column = header.dataset.column;
      sortTable(column);
    });
  });
}

/**
 * Trie le tableau par colonne
 */
function sortTable(column) {
  const table = document.querySelector(".crud-table");
  if (!table) return;

  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  // Déterminer l'ordre de tri actuel
  const currentOrder = table.dataset.sortOrder || "asc";
  const newOrder = currentOrder === "asc" ? "desc" : "asc";

  // Trouver l'index de la colonne
  const headers = table.querySelectorAll("th");
  let columnIndex = -1;
  headers.forEach((header, index) => {
    if (header.dataset.column === column) {
      columnIndex = index;
    }
  });

  if (columnIndex === -1) return;

  // Trier les lignes
  rows.sort((a, b) => {
    const cellA = a.cells[columnIndex].textContent.trim();
    const cellB = b.cells[columnIndex].textContent.trim();

    // Essayer de comparer comme des nombres
    const numA = parseFloat(cellA);
    const numB = parseFloat(cellB);

    if (!isNaN(numA) && !isNaN(numB)) {
      return newOrder === "asc" ? numA - numB : numB - numA;
    }

    // Comparaison alphabétique
    return newOrder === "asc"
      ? cellA.localeCompare(cellB)
      : cellB.localeCompare(cellA);
  });

  // Réinsérer les lignes triées
  rows.forEach((row) => tbody.appendChild(row));

  // Mettre à jour l'ordre de tri
  table.dataset.sortOrder = newOrder;
  table.dataset.sortColumn = column;
}

/**
 * Initialise les fonctionnalités du formulaire CRUD
 */
function initCrudForm() {
  const form = document.querySelector(".crud-form");
  if (!form) return;

  // Validation basique avant soumission
  form.addEventListener("submit", (e) => {
    const requiredInputs = form.querySelectorAll("[required]");
    let isValid = true;

    requiredInputs.forEach((input) => {
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add("is-invalid");
      } else {
        input.classList.remove("is-invalid");
      }
    });

    if (!isValid) {
      e.preventDefault();
      alert("Veuillez remplir tous les champs obligatoires");
    }
  });

  // Retirer la classe d'erreur lors de la saisie
  const inputs = form.querySelectorAll(
    ".crud-form-input, .crud-form-select, .crud-form-textarea"
  );
  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      input.classList.remove("is-invalid");
    });
  });
}

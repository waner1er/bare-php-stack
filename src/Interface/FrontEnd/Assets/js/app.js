import "../scss/app.scss";

document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.querySelector("[data-nav-toggle]");
    const menu = document.querySelector("[data-nav-menu]");

    if (!toggle || !menu) return;

    const setOpen = (open) => {
        toggle.setAttribute("aria-expanded", String(open));
        menu.classList.toggle("is-open", open);
    };

    toggle.addEventListener("click", (e) => {
        e.stopPropagation();
        const open = toggle.getAttribute("aria-expanded") !== "true";
        setOpen(open);
    });

    // Ferme au clic en dehors
    document.addEventListener("click", (e) => {
        if (!menu.contains(e.target) && !toggle.contains(e.target)) {
            setOpen(false);
        }
    });

    // Ferme à Echap
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") setOpen(false);
    });
});

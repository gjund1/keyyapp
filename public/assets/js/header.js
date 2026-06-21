// MENU
const menuBtn = document.querySelector(".Header-menu");
const menuOverlay = document.querySelector(".Menu");
const closeMenu = document.querySelector(".Menu-header-closeBtn");

menuBtn.addEventListener("click", () => {
    menuOverlay.classList.add("active");
});

closeMenu.addEventListener("click", () => {
    menuOverlay.classList.remove("active");
});

// fermeture en cliquant à côté
document.addEventListener("click", (e) => {
    const isClickInsideMenu = menuOverlay.contains(e.target);
    const isClickOnButton = menuBtn.contains(e.target);

    if (!isClickInsideMenu && !isClickOnButton) {
        menuOverlay.classList.remove("active");
    }
});

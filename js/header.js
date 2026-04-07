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
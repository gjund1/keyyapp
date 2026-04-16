// Filter Button
const toggleBtn = document.querySelector(".Filter-toggle");
const panel = document.querySelector(".Filter-panel");

toggleBtn.addEventListener("click", () => {
    panel.classList.toggle("active");
});
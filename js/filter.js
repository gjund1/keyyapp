// Filter Button
const toggleBtn = document.querySelector(".Filter-toggle");
const panel = document.querySelector(".Filter-panel");

toggleBtn.addEventListener("click", () => {
    panel.classList.toggle("active");
});

// Fliter date / distance

const filters = {
    sort: "date",
    city: null,
    mesBoites: true
};

document.addEventListener("DOMContentLoaded", () => {

    // TRI
    document.querySelectorAll('input[name="tri"]').forEach(input => {
        input.addEventListener("change", (e) => {
            filters.sort = e.target.value;
            applyFilters();
        });
    });

    // VILLE
    const citySelect = document.querySelector("select");
    citySelect.addEventListener("change", (e) => {
        filters.city = e.target.value;
        applyFilters();
    });

    // MES BOITES
    const checkbox = document.querySelector('input[name="mesboites"]');
    checkbox.addEventListener("change", (e) => {
        filters.mesBoites = e.target.checked;
        applyFilters();
    });

});

// Remplir les villes dans le Panel filter dynamiquement
function populateCities() {
    const select = document.querySelector("select");

    const cities = [...new Set(data.map(item => item.city))];

    select.innerHTML = `<option value="">Toutes</option>` +
        cities.map(city => `<option value="${city}">${city}</option>`).join("");
}

populateCities();

function applyFilters() {

    let filtered = [...data];

    // filtre ville
    if (filters.city) {
        filtered = filtered.filter(item => item.city === filters.city);
    }

    // filtre mes boites
    if (filters.mesBoites) {
        filtered = filtered.filter(item => item.user_id === 1);
    }

    // CALCUL de la distance (si GPS dispo)
    if (window.userLat && window.userLon) {
        filtered.forEach(item => {item.distance = getDistanceGPS(window.userLat, window.userLon, item.latitude, item.longitude); });
    } else {
        filtered.forEach(item => {item.distance = null; });
    }

    //  TRI
    if (filters.sort === "distance" && window.userLat && window.userLon) {
        filtered.sort((a, b) => a.distance - b.distance);
    } else {
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }

    // renderList
    renderList(filtered, window.userLat, window.userLon);
};
// Filter Button
const toggleBtn = document.querySelector(".Filter-toggle");
const panel = document.querySelector(".Filter-panel");

// OUVRIR / FERMER le panel
toggleBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    panel.classList.toggle("active");
});

// empêcher fermeture quand on clique DANS le panel
panel.addEventListener("click", (e) => {
    e.stopPropagation();
});

// fermer quand on clique ailleurs
document.addEventListener("click", () => {
    panel.classList.remove("active");
});

// ===================================
// Fliter date / distance / mes Boites

const filters = {
    sort: "date",
    city: null,
    mesBoites: false,
    pending:false
};

// PRIORITÉ AUX PARAMÈTRES URL
function loadFiltersFromUrl() {
    const params = new URLSearchParams(window.location.search);
    if (params.get("pending") === "1") {
        filters.pending = true;
        filters.mesBoites = false;
    }
    if (params.get("mesboites") === "1") {
        filters.mesBoites = true;
        filters.pending = false;
    }
    if (params.get("tri"))
        filters.sort = params.get("tri");
    if (params.has("city"))
        filters.city = params.get("city") || null;
    saveFilters();
}

// Sauvegarder les filtres
function saveFilters() {
    localStorage.setItem("filters", JSON.stringify(filters));
}

const savedFilters = localStorage.getItem("filters");
if (savedFilters)
    Object.assign(filters, JSON.parse(savedFilters));

loadFiltersFromUrl();

document.addEventListener("DOMContentLoaded", () => {
    // Restaurer état UI depuis localStorage
    // TRI
    const triInput = document.querySelector(`input[name="tri"][value="${filters.sort}"]`);
    if (triInput)
        triInput.checked = true;

    // VILLE
    const citySelect = document.querySelector("select");
    if (citySelect)
        citySelect.value = filters.city || "";

    // MES BOITES
    const checkbox = document.querySelector('input[name="mesboites"]');
    if (checkbox)
        checkbox.checked = filters.mesBoites;

    // PENDING  
    const checkPending = document.querySelector('input[name="pending"]');
    if (checkPending)
        checkPending.checked = filters.pending;

    // EVENTS
    // TRI
    document.querySelectorAll('input[name="tri"]').forEach(input => {
        input.addEventListener("change", (e) => {
            filters.sort = e.target.value;
            saveFilters()
            applyFilters();
        });
    });

    // VILLE
    if (citySelect) {
        citySelect.addEventListener("change", (e) => {
            filters.city = e.target.value;
            saveFilters()
            applyFilters();
        });
    }

    // MES BOITES
    if (checkbox) {
        checkbox.addEventListener("change", (e) => {
            filters.mesBoites = e.target.checked;
            saveFilters()
            applyFilters();
        });
    }

    // PENDING
    if (checkPending) {
        checkPending.addEventListener("change", (e) => {
            filters.pending = e.target.checked;
            saveFilters()
            applyFilters();
        });
    }
});

// Remplir les villes dans le Panel filter dynamiquement
function populateCities() {
    const select = document.querySelector("select");
    const cities = [...new Set(data.map(item => item.city).filter(Boolean))];
    select.innerHTML = `<option value="">Toutes</option>` + cities.map(city => `<option value="${city}">${city}</option>`).join("");
}

populateCities();
applyFilters();

function applyFilters() {

    let filtered = [...data];

    // filtre ville
    if (filters.city)
        filtered = filtered.filter(item => item.city === filters.city);

    // filtre mes boites
    if (filters.mesBoites) {
        if (filters.mesBoites && currentUserId)
            filtered = filtered.filter(item => item.user_id == currentUserId);
    }

    // filtre pending
    if (filters.pending)
        filtered = filtered.filter(item => item.status === "PENDING");

    // CALCUL de la distance (si GPS dispo)
    if (window.userLat && window.userLon)
        filtered.forEach(item => {item.distance = getDistanceGPS(window.userLat, window.userLon, item.latitude, item.longitude); });
    else
        filtered.forEach(item => {item.distance = null; });

    //  TRI
    if (filters.sort === "distance" && window.userLat && window.userLon)
        filtered.sort((a, b) => a.distance - b.distance);
    else
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    // Compteur de boites
    const countElement = document.querySelector(".Filter-panel-count");
    if (countElement)
        countElement.textContent = `${filtered.length} boîte${filtered.length > 1 ? "s" : ""} trouvée${filtered.length > 1 ? "s" : ""}`;

    // compteur top page liste
    const topCount = document.querySelector(".Main-list-top-count");
    if (topCount)
        topCount.innerHTML = `&nbsp;(${filtered.length} boîte${filtered.length > 1 ? "s" : ""} trouvée${filtered.length > 1 ? "s" : ""})`;
    
    // PAGE LISTE
    if (typeof renderList === "function") {
        displayLimit = 100;
        renderList(filtered, window.userLat, window.userLon);
    }

    // PAGE MAP
    if (typeof renderMap === "function")
        renderMap(filtered);
};
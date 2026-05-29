// ==============================
//   Icônes Leaflet en couleur
// ==============================

function createPin(color) {
    return L.divIcon({
        className: "custom-pin",
        html: `
            <svg width="32" height="32" viewBox="0 0 24 24">
                <path fill="${color}"   d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5" fill="white"/>
            </svg>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 32]
    });
}

// ========================
//    Initialisation GPS
// ========================

let poiMarkers = [];

let userLat = parseFloat(localStorage.getItem("userLat")) || 43.2965;
let userLon = parseFloat(localStorage.getItem("userLon")) || 5.3698;

// Initialisation carte centrée sur Marseille
var map = L.map('map', {maxZoom: 19}).setView([userLat, userLon], 13);
let userMarker = L.marker([userLat, userLon]).addTo(map);
let accuracyCircle = L.circle([userLat, userLon], {radius: 5000, color: 'transparent', fillColor: '#136aec', fillOpacity: 0.2}).addTo(map);

// Layer OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap', maxZoom: 19}).addTo(map);

let firstLocation = true;
window.myUserId = 1;                // utilisateur id = 1 A MIDIFIER;

// ==================================================
// fonction succes et error de navigator.geolocation
// ==================================================

function success(position) {
        userLat = position.coords.latitude;
        userLon = position.coords.longitude;
        // cache GPS
        localStorage.setItem("userLat", userLat);
        localStorage.setItem("userLon", userLon);
        // localStorage.setItem("gpsTimestamp", Date.now());
        const accuracy = position.coords.accuracy;

        // recentre la carte en zoom 13
        if (firstLocation) {
            map.setView([userLat, userLon], 13);
            firstLocation = false;
        }

        // Marker utilisateur
            userMarker.setLatLng([userLat, userLon]);

        // Cercle de précision
            accuracyCircle.setLatLng([userLat, userLon]);
            accuracyCircle.setRadius(accuracy);
    };

function error() {
    alert("Localisation refusée");
};

// ========================
// Localisation temps réel
// ========================

let watchId;

if (navigator.geolocation) {

    // GPS rapide au démarrage
    watchId = navigator.geolocation.watchPosition(success, error, {enableHighAccuracy: true, timeout: 10000, maximumAge: 0}
    );

    // Après 20 minute -> mode économie
    setTimeout(() => {
        navigator.geolocation.clearWatch(watchId);
        watchId = navigator.geolocation.watchPosition(success, error, {enableHighAccuracy: false, timeout: 15000, maximumAge: 10000});
        console.log("GPS mode économie activé");
    }, 1200000);              // 20 minute
}

// ==============================
//          Filtre POI
// ==============================
function renderMap(dataArray) {

    // Supprime anciens markers
    poiMarkers.forEach(marker => {map.removeLayer(marker); });
    poiMarkers = [];

    // Ajoute nouveaux markers
    dataArray.forEach(item => {

        const color = item.user_id === window.myUserId ? "#308aff" : "#c234c7cb";
        const marker = L.marker([item.latitude, item.longitude], {icon: createPin(color)}).addTo(map);
        const url = `fiche.php?id=${item.id}`;
        const googleUrl = `https://www.google.com/maps/dir/?api=1&destination=${item.latitude},${item.longitude}`;
        const popup = `
            <div class="popup">
                <strong>${item.city}</strong> (id ${item.id})<br>
                ${item.address || "Adresse inconnue"}<br><br>
                <a href="${url}">🔎 Voir la fiche</a><br>
                <a href="${googleUrl}" target="_blank">🚗 Itinéraire</a>
            </div>
        `;
        marker.bindPopup(popup);
        poiMarkers.push(marker);
    });
}

applyFilters();
// Initialisation carte centrée sur Marseille
var map = L.map('map', {maxZoom: 19}).setView([43.2965, 5.3698], 13);

// Layer OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap',
    maxZoom: 19
}).addTo(map);

let userLat = null;
let userLon = null;

let userMarker = null;
let firstLocation = true;
let accuracyCircle = null;

// fonction
function success(position) {
        userLat = position.coords.latitude;
        userLon = position.coords.longitude;
        const accuracy = position.coords.accuracy;

        // recentre la carte en zoom max 19    a modifier
        if (firstLocation) {
            map.setView([userLat, userLon], 18);
            firstLocation = false;
        }

        // Marker utilisateur                a modifier
        if (userMarker) {
            userMarker.setLatLng([userLat, userLon]);
        } else {
            userMarker = L.marker([userLat, userLon]).addTo(map);
        }
    }

function error() {
    alert("Localisation refusée");
};

// Localisation temps réel                  a modifier
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(success, error, {enableHighAccuracy: true})
};
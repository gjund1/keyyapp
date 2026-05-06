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

        // recentre la carte en zoom 13
        if (firstLocation) {
            map.setView([userLat, userLon], 13);
            firstLocation = false;
        }

        // Marker utilisateur
        if (userMarker) {
            userMarker.setLatLng([userLat, userLon]);
        } else {
            userMarker = L.marker([userLat, userLon]).addTo(map);
        }

        // Cercle de précision
        if (accuracyCircle) {
            accuracyCircle.setLatLng([userLat, userLon]);
            accuracyCircle.setRadius(accuracy);
        } else {
            accuracyCircle = L.circle([userLat, userLon], {
                radius: accuracy,
                color: 'transparent',
                fillColor: '#136aec',
                fillOpacity: 0.2
            }).addTo(map);
        }
    }

function error() {
    alert("Localisation refusée");
};

// Localisation temps réel
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(success, error, {enableHighAccuracy: false})      // enableHighAccuracy: true (GPS rapide et precis)
};
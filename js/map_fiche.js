// Initialisation carte centrée sur Marseille
var map = L.map('map', {maxZoom: 19}).setView([43.2965, 5.3698], 13);

// Layer OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap', maxZoom: 19}).addTo(map);

let userLat = 43.3127424;
let userLon = 5.4001664;

let userMarker = null;
let firstLocation = true;
let accuracyCircle = null;

// Position de la fiche
const cardLat = 43.3127424;
const cardLon = 5.4001664;

// Marker fiche
L.marker([cardLat, cardLon]).addTo(map);

// Centre la map sur la fiche
map.setView([cardLat, cardLon], 19);
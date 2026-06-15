const map = L.map('map');

map.setView([lat, lon], 18);

L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
).addTo(map);

L.marker([lat, lon]).addTo(map);
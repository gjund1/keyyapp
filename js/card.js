const userCLat = parseFloat(localStorage.getItem("userLat"));
const userCLon = parseFloat(localStorage.getItem("userLon"));

function getDistanceGPS(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const toRad = (deg) => deg * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) *
        Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2;
    return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function formatDistance(distanceKm) {
    if (distanceKm < 1) return Math.round(distanceKm * 1000) + " m";
    if (distanceKm < 10) return distanceKm.toFixed(1) + " km";
    return Math.round(distanceKm) + " km";
}

function printDistance(cardLat, cardLon) {
    const el = document.querySelector('.Main-card-content1-distance');

    if (!el) return;
    if (!userCLat || !userCLon || !cardLat || !cardLon) {
        el.textContent = "Distance : inconnue";
        return;
    }

    const distanceKm = getDistanceGPS(userCLat, userCLon, cardLat, cardLon);
    el.textContent = "Distance : " + formatDistance(distanceKm);
}

printDistance(cardLat, cardLon);

console.log("userLat:", userLat);
console.log("userLon:", userLon);
console.log("cardLat:", cardLat);
console.log("cardLon:", cardLon);
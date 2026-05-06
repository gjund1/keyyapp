// ------------------------------------ //
//            Position GPS              //
// ------------------------------------ //

// Fonction pour récupérer la position GPS
function getUserLocation(successCallback, errorCallback) {
    if (!navigator.geolocation) {
        errorCallback("Géolocalisation non supportée");
        return;
    };

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            successCallback(lat, lon);
        },
        function(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorCallback("Permission refusée");
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorCallback("Position indisponible");
                    break;
                case error.TIMEOUT:
                    errorCallback("Temps dépassé");
                    break;
                default:
                    errorCallback("Erreur inconnue");
            }
        },
        {
            enableHighAccuracy: false,  // GPS plus rapide      
            timeout: 60000,             // refreach max 60 secondes
        }
    );
};

// ------------------------------------ //
//               GPS List               //
// ------------------------------------ //

// affichage instantané sans GPS
applyFilters();

// Recupere la geolocation pour la list
getUserLocation(
    function(lat, lon) {
        window.userLat = lat;
        window.userLon = lon;
        applyFilters();
    },
    function(errorMsg) {
        console.warn(errorMsg);
        
        // fallback Marseille
        window.userLat = 43.2965;
        window.userLon = 5.3698;
        applyFilters();
    }
);

// ------------------------------------ //
//          Distance avec GPS           //
// ------------------------------------ //

// Calcul de la distance en km
function getDistanceGPS(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const toRad = (deg) => deg * Math.PI / 180;

    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) *
        Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // distance en km
};

// Format de la distance km ou m
function formatDistance(distanceKm) {
    if (distanceKm < 1) {
        return Math.round(distanceKm * 1000) + " m";
    } else if (distanceKm < 10) {
        return distanceKm.toFixed(1) + " km";
    } else {
        return Math.round(distanceKm) + " km";
    }
};

// Affichage de la distance
function printDistance(item, userLat, userLon) {
    let distance = null;

    if (item) {
        const distKm = getDistanceGPS(userLat, userLon, item.latitude, item.longitude);
        distance = formatDistance(distKm);
    } else {
        distance = "distance inconnue";
    }
    return distance;
};

// ------------------------------------ //
//               Status                 //
// ------------------------------------ //

// Fonction pour la couleur du Status
function getStatusClass(status) {
    switch (status) {
        case "VALIDATED":
            return "status-green";
        case "PENDING":
            return "status-orange";
        case "REFUSED":
            return "status-red";
        case "CANCELED":
            return "status-red";
        default:
            return "";
    }
};

// Fonction pour le texte du Status
function getStatusTexte(status) {
    switch (status) {
        case "VALIDATED":
            return "validé";
        case "PENDING":
            return "en attente";
        case "REFUSED":
            return "refusé";
        case "CANCELED":
            return "supprimé";
        default:
            return "";
    }
};

// Affiche le Status uniquement pour mes boites
function getStatusHtml(item, user_id) {
    let statusHtml = "";
    if (item.user_id === user_id) 
        statusHtml = `statut : <span class="Item-content-statut-span ${getStatusClass(item.status)}">${getStatusTexte(item.status)}</span>`;
    return statusHtml;
};

// ------------------------------------ //
//               il y a 3 jours..       //
// ------------------------------------ //

function timeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) 
        return "Ajoutée à l’instant";
    if (diff < 3600) 
        return `Ajoutée il y a ${Math.floor(diff / 60)} minutes`;
    if (diff < 86400) 
        return `Ajoutée il y a ${Math.floor(diff / 3600)} heures`;
    if (diff < 2592000) 
        return `Ajoutée il y a ${Math.floor(diff / 86400)} jours`;
    if (diff < 31536000) 
        return `Ajoutée il y a ${Math.floor(diff / 2592000)} mois`;
    
    return `Ajoutée il y a ${Math.floor(diff / 31536000)} an`;
}

// ------------------------------------ //
//                CARD                  //
// ------------------------------------ //

function renderList(dataArray, userLat, userLon) {

    const list = document.getElementById("list");

    const html = dataArray.map(item => `
        <a href="fiche.php?id=${item.id}" class="Main-list-article-link">
            <article class="Main-list-article-link-item Item">
                <img src="${item.image}" class="Item-photo">
                <div class="Item-content">
                    <div class="Item-content-box">
                        <p class="Item-content-box-ville">
                            <span class="Item-content-box-ville-span">${item.city}</span> (${item.cp})</p>
                        <p class="Item-content-box-dep">${item.region} (${item.pays})</p>
                    </div>
                    <p class="Item-content-quartier">Quartier : ${item.quartier}</p>
                    <br><br>
                    <p class="Item-content-date">${timeAgo(item.created_at)}</p>
                    <div class="Item-content-box">
                        <p class="Item-content-box-km">Distance : ${item.distance !== null && item.distance !== undefined ? formatDistance(item.distance) : "..."}</p>
                        <p class="Item-content-statut">${getStatusHtml(item, 1)}</p>
                    </div>
                </div>
            </article>
        </a>
    `).join("");

    list.innerHTML = html;
};
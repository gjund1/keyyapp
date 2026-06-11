let displayLimit = 100;

// ------------------------------------ //
//            Position GPS              //
// ------------------------------------ //

// Fonction pour récupérer la position GPS
function getUserLocation(successCallback, errorCallback) {
    if (!navigator.geolocation) {
        errorCallback("Géolocalisation non supportée");
        return;
    };

    // Tentative GPS rapide/précis
    navigator.geolocation.getCurrentPosition(
        function(position) {
            successCallback(position.coords.latitude, position.coords.longitude);
        },

        function(error) {
            if (error.code === error.TIMEOUT) {
                console.warn("GPS précis timeout -> mode éco");
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        successCallback(position.coords.latitude, position.coords.longitude);
                    },

                    function(error) {
                        errorCallback(error.message);
                    },
                    {
                        enableHighAccuracy: false,
                        timeout: 15000,
                        maximumAge: 300000
                    }
                );
            } else {
                errorCallback(error.message);
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );
};

// ------------------------------------ //
//               GPS List               //
// ------------------------------------ //

const cachedLat = localStorage.getItem("userLat");
const cachedLon = localStorage.getItem("userLon");

window.userLat = cachedLat ? parseFloat(cachedLat) : 43.2965;
window.userLon = cachedLon ? parseFloat(cachedLon) : 5.3698;

// affichage instantané sans GPS
applyFilters();

// Recupere la geolocation pour la list
getUserLocation(
    function(lat, lon) {
        window.userLat = lat;
        window.userLon = lon;

        // cache GPS
        localStorage.setItem("userLat", lat);
        localStorage.setItem("userLon", lon);
        // localStorage.setItem("gpsTimestamp", Date.now());

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

function getStatusClassItem(item, user_id) {
    if (item.user_id === user_id) 
        return "myItem";
}

// ------------------------------------ //
//               il y a 3 jours..       //
// ------------------------------------ //

function timeAgo(date) {
    date = new Date(date);
    if (isNaN(date.getTime()))
        return "Date inconnue";

    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60)
        return "Ajoutée à l’instant";
    if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        return `Ajoutée il y a ${minutes} minute${minutes > 1 ? "s" : ""}`;
    }
    if (diff < 86400) {
        const hours = Math.floor(diff / 3600);
        return `Ajoutée il y a ${hours} heure${hours > 1 ? "s" : ""}`;
    }
    if (diff < 2592000) {
        const days = Math.floor(diff / 86400);
        return `Ajoutée il y a ${days} jour${days > 1 ? "s" : ""}`;
    }
    if (diff < 31536000) {
        const months = Math.floor(diff / 2592000);
        return `Ajoutée il y a ${months} mois`;
    }
    const years = Math.floor(diff / 31536000);
    return `Ajoutée il y a ${years} an${years > 1 ? "s" : ""}`;
}

// ------------------------------------ //
//                CARD                  //
// ------------------------------------ //

function renderList(dataArray, userLat, userLon) {
    const list = document.getElementById("list");

    const visibleItems = dataArray.slice(0, displayLimit);
    const html = visibleItems.map(item => `
        <a href="card.php?id=${item.id}" class="Main-list-article-link">
            <article class="Main-list-article-link-item Item ${getStatusClassItem(item, currentUserId)}">
                <img src="${item.image}" class="Item-photo">
                <div class="Item-content">
                    <div class="Item-content-box">
                        <p class="Item-content-box-ville">
                            <span class="Item-content-box-ville-span">${item.city}</span>
                            <span class="Item-content-box-cp"> (${item.cp})</span>
                        </p>
                        <p class="Item-content-box-dep">${item.region}
                            <span class="Item-content-box-pays"> (${item.pays})</span>
                        </p>
                    </div>
                    <div class="Item-content-inside">
                        <p class="Item-content-quartier">Quartier : ${item.quartier}</p>
                        
                        <p class="Item-content-date">${timeAgo(item.created_at)}</p>
                    </div>
                    <div class="Item-content-box">
                        <p class="Item-content-box-km">Distance : ${item.distance !== null && item.distance !== undefined ? formatDistance(item.distance) : "..."}</p>
                        <p class="Item-content-statut">${getStatusHtml(item, currentUserId)}</p>
                    </div>
                </div>
            </article>
        </a>
    `).join("");

    list.innerHTML = html;

    // boutton voir plus +100 >>
    const oldBtn = document.getElementById('btn-load-more');
    if (oldBtn)
        oldBtn.remove();

    if (dataArray.length > displayLimit) {
        const btn = document.createElement('button');
        btn.id = 'btn-load-more';
        btn.className = 'Btn';
        btn.textContent = `Voir plus (${dataArray.length - visibleItems.length} restants)`;
        btn.addEventListener('click', () => {
            displayLimit += 50;
            renderList(dataArray, userLat, userLon);
        });
        let btnContainer = document.getElementById('loadMoreContainer');

        if (!btnContainer) {
            btnContainer = document.createElement('div');
            btnContainer.id = 'loadMoreContainer';
            list.parentNode.appendChild(btnContainer);
        }

        btnContainer.innerHTML = '';
        btnContainer.appendChild(btn);
    }
};
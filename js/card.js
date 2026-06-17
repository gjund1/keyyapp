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

// ==================
//   LIGHTBOX PHOTO
// ==================

const image = document.getElementById('poiImage');
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightboxImg');
const closeBtn = document.getElementById('lightboxClose');

if (image && lightbox && lightboxImg && closeBtn) {
    function openLightbox() {
        lightboxImg.src = image.src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    image.addEventListener('click', openLightbox);
    closeBtn.addEventListener('click', closeLightbox);

    // clic sur fond noir
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox)
            closeLightbox();
    });

    // touche ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape')
            closeLightbox();
    });
}

/* =================================== */
/*              Popin                  */
/* =================================== */

const btnDelete = document.querySelector(".Main-card-btn-del");
const deleteModal = document.getElementById("deleteModal");
const btnCancelDelete = document.getElementById("btnCancelDelete");

btnDelete.addEventListener("click", () => {
    deleteModal.classList.add("active");
});

btnCancelDelete.addEventListener("click", () => {
    deleteModal.classList.remove("active");
});

deleteModal.addEventListener("click", e => {
    if (e.target === deleteModal)
        deleteModal.classList.remove("active");
});
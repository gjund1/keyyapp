// location Button
const locateBtn = document.querySelector(".Main-button-locate");

if (locateBtn) {
    locateBtn.addEventListener("click", () => {
        if (userLat && userLon) {
            map.setView([userLat, userLon], 16);
            // if (userMarker) {
            //     userMarker.openPopup();
            // }
        } else {
            alert("Position non disponible");
        }
    });
};
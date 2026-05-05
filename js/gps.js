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
        }
    );
};
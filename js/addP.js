// =============
//  CONSTANTES
// =============
const DOM = {
  gpsInfo: document.getElementById("gpsInfo"),
  video: document.getElementById("video"),
  btnCapture: document.getElementById("btnCapture"),
  canvas: document.getElementById("canvas"),
  map: L.map("map").setView([43.2965, 5.3698], 15),
};

// Initialisation de la carte OpenStreetMap
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(DOM.map);

// =============
//  VARIABLES
// =============
let state = {
  marker: null,
  lat: null,
  lon: null,
  accuracy: 999,
  stream: null,
};

// =============
//  FONCTIONS UTILITAIRES
// =============

/**
 * Affiche une erreur dans la console et dans l'UI.
 * @param {Error|string} error - L'erreur à afficher.
 */
function handleError(error) {
  console.error(error);
  DOM.gpsInfo.textContent = `Erreur : ${error.message || error}`;
  DOM.gpsInfo.style.color = "red";
}

/**
 * Met à jour le marqueur GPS sur la carte.
 * @param {number} lat - Latitude.
 * @param {number} lon - Longitude.
 */
function updateGPSMarker(lat, lon) {
  if (state.marker) state.marker.remove();
  state.marker = L.marker([lat, lon]).addTo(DOM.map);
  DOM.map.setView([lat, lon], 18);
}

/**
 * Active le bouton de capture si la précision GPS est suffisante.
 */
function checkGPSAccuracy() {
  DOM.btnCapture.disabled = false;
  DOM.btnCapture.textContent = "Capturer";
}

/**
 * Compresse une image pour qu'elle fasse moins de 2 Mo.
 * @param {Blob} blob - L'image à compresser.
 * @returns {Promise<File>} - L'image compressée sous forme de File.
 */
async function compressImage(blob) {
  return new Promise((resolve) => {
    const img = new Image();
    img.onload = () => {
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");
      const maxWidth = 1280;
      const scale = maxWidth / img.width;

      canvas.width = maxWidth;
      canvas.height = img.height * scale;

      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

      canvas.toBlob(
        (compressedBlob) => {
          resolve(new File([compressedBlob], "photo.jpg", { type: "image/jpeg" }));
        },
        "image/jpeg",
        0.8
      );
    };
    img.src = URL.createObjectURL(blob);
  });
}

// =============
//  INITIALISATION
// =============

/**
 * Initialise la caméra.
 */
async function initCamera() {
  try {
    state.stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: "environment" },
      audio: false,
    });
    DOM.video.srcObject = state.stream;
  } catch (error) {
    handleError(new Error(`Erreur caméra : ${error.message}`));
  }
}

/**
 * Initialise le suivi GPS.
 */
function initGPS() {
  if (!navigator.geolocation) {
    handleError(new Error("La géolocalisation n'est pas supportée par votre navigateur."));
    return;
  }

  navigator.geolocation.watchPosition(
    (position) => {
      state.lat = position.coords.latitude;
      state.lon = position.coords.longitude;
      state.accuracy = position.coords.accuracy;

      DOM.gpsInfo.textContent = `GPS : ${Math.round(state.accuracy)} m`;
      DOM.gpsInfo.style.color = state.accuracy <= 1555 ? "green" : "orange";

      updateGPSMarker(state.lat, state.lon);
      checkGPSAccuracy();
    },
    handleError,
    { enableHighAccuracy: true }
  );
}

/**
 * Capture une photo et l'envoie au serveur.
 */
async function capturePhoto() {
  try {
    DOM.canvas.width = DOM.video.videoWidth;
    DOM.canvas.height = DOM.video.videoHeight;

    const ctx = DOM.canvas.getContext("2d");
    ctx.drawImage(DOM.video, 0, 0);

    const blob = await new Promise((resolve) => DOM.canvas.toBlob(resolve, "image/jpeg", 0.9));
    const compressedFile = await compressImage(blob);

    const formData = new FormData();
    formData.append("photo", compressedFile);
    formData.append("lat", state.lat);
    formData.append("lon", state.lon);

    const response = await fetch("add_upload.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();
    if (data.success) {
      window.location.href = "add_confirm.php";
    } else {
      handleError(new Error("Échec de l'envoi de la photo."));
    }
  } catch (error) {
    handleError(error);
  }
}

// =============
//  ÉCOUTEURS D'ÉVÉNEMENTS
// =============
DOM.btnCapture.addEventListener("click", capturePhoto);

// =============
//  DÉMARRAGE
// =============
window.addEventListener("DOMContentLoaded", () => {
  initCamera();
  initGPS();
});

// Nettoyage des ressources au départ de la page
window.addEventListener("beforeunload", () => {
  if (state.stream) {
    state.stream.getTracks().forEach((track) => track.stop());
  }
});
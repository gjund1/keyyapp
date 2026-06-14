//============
//   INIT
// ===========

const gpsInfo = document.getElementById("gpsInfo");
const video = document.getElementById("video");
const btnCapture = document.getElementById("btnCapture");
const canvas = document.getElementById("canvas");

const map = L.map("map").setView([43.2965, 5.3698], 15);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

let marker = null;

let lat = null;
let lon = null;
let accuracy = 999;
let geoData = null;

// ===========
//  GPS LIVE
// ===========

navigator.geolocation.watchPosition(
    async pos => {

        lat = pos.coords.latitude;
        lon = pos.coords.longitude;
        accuracy = pos.coords.accuracy;

        gpsInfo.textContent =
            `GPS : ${Math.round(accuracy)} m`;

        if (marker) marker.remove();

        marker = L.marker([lat, lon]).addTo(map);
        map.setView([lat, lon], 18);

        if (!geoData && (accuracy <= 15)) {

            geoData = await reverseGeocode(lat, lon);

            btnCapture.disabled = false;
            btnCapture.textContent = "📷 Capturer";
        }
    },

    err => {

        console.warn(err);
    },
    {
        enableHighAccuracy: true
    }
);

// CAMERA LIVE (ARrière)
// ======================

navigator.mediaDevices.getUserMedia({
    video: {
        facingMode: "environment"
    }
})
.then(stream => {
    video.srcObject = stream;
})
.catch(err => {
    console.warn("Camera error", err);
});

// CAPTURE + COMPRESSION
// =======================

btnCapture.addEventListener("click", async () => {

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0);

    canvas.toBlob(async blob => {

        const file = await compressImage(blob);

        const formData = new FormData();

        formData.append("photo", file);
        formData.append("lat", lat);
        formData.append("lon", lon);

        formData.append("address", geoData.address);
        formData.append("city", geoData.city);
        formData.append("quartier", geoData.quartier);
        formData.append("cp", geoData.cp);
        formData.append("region", geoData.region);
        formData.append("pays", geoData.pays);

        const res = await fetch("add_upload.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            window.location = "add_confirm.php";
        }

    }, "image/jpeg", 0.9);
});

// COMPRESSION IMAGE (<2 Mo)

async function compressImage(blob) {

    return new Promise(resolve => {

        const img = new Image();

        img.onload = () => {

            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            const maxW = 1280;

            const scale = maxW / img.width;

            canvas.width = maxW;
            canvas.height = img.height * scale;

            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(
                b => resolve(
                    new File([b], "photo.jpg", {
                        type: "image/jpeg"
                    })
                ),
                "image/jpeg",
                0.8
            );
        };

        img.src = URL.createObjectURL(blob);
    });
}

// REVERSE GEOCODING (simple)
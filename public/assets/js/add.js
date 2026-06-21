//============
//   INIT
// ===========

const gpsInfo = document.getElementById("gpsInfo");
const video = document.getElementById("video");
const btnCapture = document.getElementById("btnCapture");
const canvas = document.getElementById("canvas");
const mapBox = document.getElementById("map");

const map = L.map("map").setView([43.2965, 5.3698], 15);
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

let marker = null;

let lat = null;
let lon = null;
let accuracyCircle = null;
let accuracy = 999;

function gpsAccuracy() {
    if (accuracy < 118000) {                                            // 11
        btnCapture.disabled = false;
        btnCapture.textContent = "Capturer";
        gpsInfo.textContent = `GPS ✅ ${Math.round(accuracy)} m`;
        mapBox.classList.add("Add-map-box-green");
        btnCapture.style.cursor = 'pointer';

    } else if (accuracy < 21) {
        gpsInfo.textContent = `GPS ⚠️ ${Math.round(accuracy)} m`;
        mapBox.classList.add("Add-map-box-orange");
    } else {
        gpsInfo.textContent = `GPS ❌ ${Math.round(accuracy)} m`;
        mapBox.classList.add("Add-map-box-red");
    }
}

// ===========
//  GPS LIVE
// ===========

navigator.geolocation.watchPosition(
    async pos => {
        lat = pos.coords.latitude;
        lon = pos.coords.longitude;
        accuracy = pos.coords.accuracy;
        
        if (marker) 
            marker.remove();

        if (accuracyCircle)
            accuracyCircle.remove();

        accuracyCircle = L.circle([lat, lon], { radius: accuracy }).addTo(map);

        marker = L.marker([lat, lon]).addTo(map);
        map.setView([lat, lon], 18);
        L.circle([lat, lon], {radius: accuracy});
        gpsAccuracy();
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
        // facingMode: "environment"
        facingMode: "user"
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

    if (!blob) {
        console.error("Blob vide !");
        return;
    }

    const file = await compressImage(blob);
    // console.log("FILE:", file);
    const formData = new FormData();
    formData.append("photo", file);
    formData.append("lat", lat);
    formData.append("lon", lon);

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

// COMPRESSION IMAGE

async function compressImage(blob) {
    return new Promise(resolve => {
        const img = new Image();

        img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const maxW = 800;
            const scale = maxW / img.width;

            canvas.width = maxW;
            canvas.height = img.height * scale;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(b => resolve(new File([b], "photo.webp", {type: "image/webp"})), "image/webp", 0.8);
        };

        img.src = URL.createObjectURL(blob);
    });
}
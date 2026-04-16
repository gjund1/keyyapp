const data = [
    {
        id: 1,
        image: "https://picsum.photos/400/300?1",
        address: "12 rue Paradis",
        city: "Marseille",
        distance: "200m",
        date: "Ajoutée il y a 2 jours"
    },
    {
        id: 2,
        image: "https://picsum.photos/400/300?2",
        address: "Vieux-Port",
        city: "Marseille",
        distance: "500m",
        date: "Ajoutée il y a 5 jours"
    },
    {
        id: 3,
        image: "https://picsum.photos/400/300?3",
        address: "Cours Mirabeau",
        city: "Aix-en-Provence",
        distance: "1.2km",
        date: "Ajoutée il y a 1 semaine"
    }
];

const list = document.getElementById("list");

function renderList() {
    list.innerHTML = "";

    data.forEach(item => {
        const card = document.createElement("div");
        card.className = "card";

        card.innerHTML = `
            <div class="card-img">
                <img src="${item.image}" alt="">
                <div class="badge">Boîte à clé</div>
                <div class="distance">${item.distance}</div>
            </div>

            <div class="card-content">
                <div class="card-title">📍 ${item.address}</div>
                <div class="card-sub">${item.city}</div>
                <div class="card-date">${item.date}</div>
            </div>
        `;

        card.addEventListener("click", () => {
            alert("Afficher sur la carte (future feature)");
        });

        list.appendChild(card);
    });
}

renderList();
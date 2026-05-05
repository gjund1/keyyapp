<link rel="stylesheet" href="css/filter.css">
<script src="js/filter.js" defer></script>

<!-- Filter Button -->
<aside class="Main-filter Filter">
    <div class="Filter-toggle"><span class="material-symbols-outlined font-arrow">arrow_right</span></div>
    <div class="Filter-panel">
        <h3 class="Filter-panel-title">Affinez votre<br>Recherche</h3>
        <br>
        <label class="Filter-panel-label">Trier par :</label>
        <input name="tri" type="radio" name="date" checked> date
        <input name="tri" type="radio" name="distance"> distance

        <br><br>
        <label class="Filter-panel-label">Filtrer par Ville :</label>
        <select>
            <option>Marseille</option>
        </select>
        <br><br>
        <input type="checkbox" name="mesboites" checked> Mes boites
    </div>
</aside>
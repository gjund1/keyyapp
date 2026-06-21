<link rel="stylesheet" href="../../public/assets/css/filter.css">
<!-- <script src="../../public/assets/js/filter.js" defer></script> -->

<!-- Filter Button -->
<aside class="Main-filter Filter">
    <div class="Filter-toggle">
        <span class="material-symbols-outlined font-arrow">arrow_right</span>
    </div>
    <div class="Filter-panel">
        <h3 class="Filter-panel-title">Affinez votre<br>Recherche</h3>
        <p class="Filter-panel-count"></p>
        <br>
        <label class="Filter-panel-label">Trier par :</label>
        <input type="radio" name="tri" value="date" checked> Date
        <input type="radio" name="tri" value="distance"> Distance

        <br><br>
        <label class="Filter-panel-label">Filtrer par Ville :</label>
        <select>
            <option>Marseille</option>
        </select>
        <br><br>
        <input type="checkbox" name="mesboites"> Mes boites
        <br>
        <?php if (isModerator() || isAdmin()) : ?>
            <input type="checkbox" name="pending"> <span class="pending">En attente</span>
        <?php endif; ?>
    </div>
</aside>
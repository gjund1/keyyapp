# 📍 KeyMap

> Application web mobile-first de recensement et de visualisation de "boîtes à clés" dans l’espace public

---

## 🧭 Contexte

Dans de nombreuses villes, notamment à Marseille, on observe la présence croissante de boîtes à clés installées dans l’espace public (façades, grilles, mobilier urbain).

Ces dispositifs sont utilisés dans le cadre de locations de courte durée.

Aujourd’hui, leur recensement repose principalement sur des relevés manuels peu digitalisés.

Ce projet propose une solution web simple permettant de **collecter, centraliser et visualiser ces éléments sur une carte interactive**.

---

## 🎯 Objectif du projet

Développer une application web mobile-first permettant :

- 📍 Ajouter des points d’accès (POI) géolocalisés
- 📸 Associer une photo et un commentaire à chaque point
- 🗺️ Visualiser les données sur une carte interactive
- 📱 Utiliser l’application directement depuis un smartphone (sans installation)

---

## 👥 Public cible

- 🏛️ Agents municipaux / urbanisme
- 🧍 Particuliers
- 🏠 Propriétaires / bailleurs

---

## ⚙️ Fonctionnalités

### 👤 Utilisateurs non inscrits

- Ajout limité à 5 POI (par IP)
- Modification / suppression de leurs propres points
- Consultation de la carte

### 👤 Utilisateurs inscrits

- Ajout illimité de POI
- Gestion de leurs points
- Demande de suppression de POI tiers

### 🛡️ Administrateur

- Validation / refus des contenus
- Modération des images et commentaires
- Suppression de tout POI
- Gestion des utilisateurs

---

## 🚦 Règles de gestion

- Accès libre sans inscription
- Droits selon type utilisateur
- Modération des contenus
- Aucune donnée sensible collectée

---

## 🧱 Stack technique

### Frontend

- HTML5 / CSS3
- JavaScript (ES6)
- Leaflet.js (OpenStreetMap)
- Geolocation API

### Backend

- PHP 8

### Base de données

- MySQL / MariaDB

---

## 🏗️ Architecture

Frontend → PHP → Base de données MySQL

Architecture MVC côté backend.

---

## 🗃️ Base de données

Tables principales :

- USERS
- POIS
- PHOTOS
- DEMANDE_SUPPRESSION

---

## 📌 Cas d’utilisation

- Ajouter un POI
- Consulter la carte
- Modifier / supprimer un POI
- Valider les contenus (admin)

---

## 🧠 Compétences mobilisées

### Frontend

- UI responsive mobile-first
- JavaScript interactif
- API geo

### Backend

- PHP / API REST
- Base de données relationnelle
- Gestion des droits

---

## 🚀 Évolutions possibles

- Filtres géographiques
- Export CSV
- PWA
- IA de modération
- Gestion avancée des rôles

---

## 🧪 Tests

- Ajout de POI
- Affichage carte
- API
- Gestion des erreurs

---

## 📦 Déploiement

- Apache
- PHP 8
- MySQL
- Gestion upload images

---

## 🎓 Contexte pédagogique

Projet réalisé dans le cadre du Titre Professionnel Développeur Web et Web Mobile.

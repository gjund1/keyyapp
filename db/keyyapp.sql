-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 28 sep. 2026 à 12:35
-- Version du serveur : 11.4.13-MariaDB
-- Version de PHP : 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données : `keyyapp`
--

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pois`
--

CREATE TABLE `pois` (
  `id` int(11) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(9,6) NOT NULL,
  `content` text DEFAULT NULL,
  `status` enum('PENDING','VALIDATED','REFUSED','CANCELED') NOT NULL DEFAULT 'PENDING',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `visibility` enum('PUBLIC','PRIVATE') NOT NULL DEFAULT 'PUBLIC',
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `quartier` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `pays` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poi_reports`
--

CREATE TABLE `poi_reports` (
  `id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `poi_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `pwd_hash` varchar(255) DEFAULT NULL,
  `role` enum('ANONYMOUS','USER','MODERATOR','ADMIN','MANAGER') NOT NULL DEFAULT 'USER',
  `auth_provider` enum('LOCAL','GOOGLE') NOT NULL DEFAULT 'LOCAL',
  `provider_user_id` varchar(255) DEFAULT NULL,
  `cookie_id` char(64) DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poi_id` (`poi_id`);

--
-- Index pour la table `pois`
--
ALTER TABLE `pois`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `poi_reports`
--
ALTER TABLE `poi_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `poi_id` (`poi_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `cookie_id` (`cookie_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `provider_user_id` (`provider_user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `pois`
--
ALTER TABLE `pois`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT pour la table `poi_reports`
--
ALTER TABLE `poi_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`);

--
-- Contraintes pour la table `pois`
--
ALTER TABLE `pois`
  ADD CONSTRAINT `pois_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `poi_reports`
--
ALTER TABLE `poi_reports`
  ADD CONSTRAINT `poi_reports_ibfk_1` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`),
  ADD CONSTRAINT `poi_reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;
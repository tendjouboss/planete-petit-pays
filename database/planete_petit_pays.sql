-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 07 août 2025 à 20:42
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `planete_petit_pays`
--

-- --------------------------------------------------------

--
-- Structure de la table `achats`
--

DROP TABLE IF EXISTS `achats`;
CREATE TABLE IF NOT EXISTS `achats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fichier_id` int NOT NULL,
  `transaction_id` int DEFAULT NULL,
  `date_achat` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_achat` (`user_id`,`fichier_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_fichier` (`fichier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `albums`
--

DROP TABLE IF EXISTS `albums`;
CREATE TABLE IF NOT EXISTS `albums` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `description` text,
  `date_sortie` date DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `prix_album` decimal(10,2) DEFAULT '0.00',
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_titre` (`titre`),
  KEY `idx_date_sortie` (`date_sortie`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `albums`
--

INSERT INTO `albums` (`id`, `titre`, `description`, `date_sortie`, `image_url`, `prix_album`, `actif`, `date_creation`) VALUES
(1, 'Premier Album', 'Le premier album de l\'artiste', '2024-01-15', NULL, '25.00', 1, '2025-08-06 14:09:15'),
(4, 'la determination', 'je montre ma determination', '2025-08-13', 'assets/uploads/albums/album_68949c29a65c89.21603795.png', '0.00', 1, '2025-08-07 00:36:48'),
(3, 'ma vie', 'tres belle histoire de notre vie', '2025-08-15', 'assets/uploads/albums/album_68949b43a71082.30411598.png', '25.00', 1, '2025-08-06 19:05:36'),
(5, 'direction', 'le vrai but', '2025-08-09', 'assets/uploads/albums/album_68941612336436.31497540.png', '0.00', 1, '2025-08-07 04:57:22'),
(6, 'meilleur vie', 'notre vie de nos jours', '2025-08-15', 'assets/uploads/albums/album_68949bff69d372.70183897.png', '0.00', 1, '2025-08-07 13:51:51');

-- --------------------------------------------------------

--
-- Structure de la table `fichiers`
--

DROP TABLE IF EXISTS `fichiers`;
CREATE TABLE IF NOT EXISTS `fichiers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `album_id` int DEFAULT NULL,
  `titre` varchar(200) NOT NULL,
  `type` enum('audio','video') NOT NULL,
  `url` varchar(255) NOT NULL,
  `prix` decimal(10,2) DEFAULT '5.00',
  `duree` varchar(10) DEFAULT NULL,
  `taille_fichier` bigint DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_album` (`album_id`),
  KEY `idx_type` (`type`),
  KEY `idx_prix` (`prix`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `fichiers`
--

INSERT INTO `fichiers` (`id`, `album_id`, `titre`, `type`, `url`, `prix`, `duree`, `taille_fichier`, `actif`, `date_creation`) VALUES
(1, 1, 'Chanson 1', 'audio', 'uploads/audio/chanson1.mp3', '5.00', '03:45', NULL, 1, '2025-08-06 14:09:15'),
(2, 1, 'Chanson 2', 'audio', 'uploads/audio/chanson2.mp3', '5.00', '04:12', NULL, 1, '2025-08-06 14:09:15'),
(8, 5, 'c&#039;est elle', 'audio', 'assets/uploads/audios/audio_68941654094a80.99598523.mp3', '5.00', '5', NULL, 1, '2025-08-07 04:58:28'),
(6, 3, 'la fin', 'audio', 'assets/uploads/audio/audio_6893d7ccd87b40.59661334.mp3', '5.00', '3', 8733124, 1, '2025-08-07 00:31:40'),
(5, 3, 'mes ami(e)s', 'audio', 'assets/uploads/audio/audio_68938e9d13c661.98854146.mp3', '5.01', '', 5679016, 1, '2025-08-06 19:19:25'),
(7, 4, 'détermination', 'video', 'assets/uploads/video/video_6893d99ff1a796.78376028.mp4', '5.00', '5', 6980653, 1, '2025-08-07 00:39:27'),
(9, 6, 'c&#039;est elle', 'audio', 'assets/uploads/audios/audio_689493c93de7f5.31473362.mp3', '5.00', '5', NULL, 1, '2025-08-07 13:53:45');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_telechargement`
--

DROP TABLE IF EXISTS `sessions_telechargement`;
CREATE TABLE IF NOT EXISTS `sessions_telechargement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fichier_id` int NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_expiration` datetime NOT NULL,
  `utilise` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fichier_id` (`fichier_id`),
  KEY `idx_token` (`token`(250)),
  KEY `idx_expiration` (`date_expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type_paiement` enum('abo','unique') NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','paye','annule') DEFAULT 'en_attente',
  `reference_paiement` varchar(100) DEFAULT NULL,
  `date_transaction` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_date` (`date_transaction`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `abonnement_actif` tinyint(1) DEFAULT '0',
  `date_abonnement` datetime DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` enum('user','admin') DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_abonnement` (`abonnement_actif`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `mot_de_passe`, `abonnement_actif`, `date_abonnement`, `date_creation`, `role`) VALUES
(1, 'Admin', 'admin@planete-petit-pays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, NULL, '2025-08-06 14:09:15', 'admin'),
(2, 'tendjou', 'parkertendjou@gmail.com', '$2y$10$qr1.2.H75qNci1kfbR3vIeIkQOlnZbzJyrDkdCIkQYV.0ZMnUmYh.', 0, NULL, '2025-08-06 15:09:16', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

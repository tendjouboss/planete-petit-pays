-- =====================================================
-- SCHÉMA DE BASE DE DONNÉES - PLATEFORME MUSICALE
-- =====================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS planete_petit_pays CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE planete_petit_pays;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    abonnement_actif BOOLEAN DEFAULT FALSE,
    date_abonnement DATETIME NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    role ENUM('user', 'admin') DEFAULT 'user',
    INDEX idx_email (email),
    INDEX idx_abonnement (abonnement_actif)
);

-- Table des albums
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    date_sortie DATE,
    image_url VARCHAR(255),
    prix_album DECIMAL(10,2) DEFAULT 0.00,
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_titre (titre),
    INDEX idx_date_sortie (date_sortie)
);

-- Table des fichiers (musiques/vidéos)
CREATE TABLE fichiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT,
    titre VARCHAR(200) NOT NULL,
    type ENUM('audio', 'video') NOT NULL,
    url VARCHAR(255) NOT NULL,
    prix DECIMAL(10,2) DEFAULT 5.00,
    duree VARCHAR(10), -- Format: MM:SS
    taille_fichier BIGINT, -- En bytes
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
    INDEX idx_album (album_id),
    INDEX idx_type (type),
    INDEX idx_prix (prix)
);

-- Table des transactions de paiement
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type_paiement ENUM('abo', 'unique') NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'paye', 'annule') DEFAULT 'en_attente',
    reference_paiement VARCHAR(100),
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_transaction)
);

-- Table des achats de fichiers
CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fichier_id INT NOT NULL,
    transaction_id INT,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fichier_id) REFERENCES fichiers(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    UNIQUE KEY unique_achat (user_id, fichier_id),
    INDEX idx_user (user_id),
    INDEX idx_fichier (fichier_id)
);

-- Table des sessions de téléchargement (sécurité)
CREATE TABLE sessions_telechargement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    fichier_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    date_expiration DATETIME NOT NULL,
    utilise BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fichier_id) REFERENCES fichiers(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiration (date_expiration)
);

-- Insertion d'un administrateur par défaut
INSERT INTO users (nom, email, mot_de_passe, role) VALUES 
('Admin', 'admin@planete-petit-pays.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insertion d'albums d'exemple
INSERT INTO albums (titre, description, date_sortie, prix_album) VALUES 
('Premier Album', 'Le premier album de l\'artiste', '2024-01-15', 25.00),
('Deuxième Album', 'Le deuxième album de l\'artiste', '2024-06-20', 30.00);

-- Insertion de fichiers d'exemple
INSERT INTO fichiers (album_id, titre, type, url, prix, duree) VALUES 
(1, 'Chanson 1', 'audio', 'uploads/audio/chanson1.mp3', 5.00, '03:45'),
(1, 'Chanson 2', 'audio', 'uploads/audio/chanson2.mp3', 5.00, '04:12'),
(1, 'Clip Vidéo 1', 'video', 'uploads/video/clip1.mp4', 5.00, '04:30'),
(2, 'Nouvelle Chanson', 'audio', 'uploads/audio/nouvelle.mp3', 5.00, '03:58'); 
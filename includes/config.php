<?php
/**
 * Configuration de la base de données
 * Plateforme Musicale - Planète Petit Pays
 */

// Configuration de la base de données - Variables d'environnement Railway
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'planete_petit_pays');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// Configuration de l'application
define('SITE_NAME', 'Planète Petit Pays');
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost/planete-petit-pays');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');

// Configuration des paiements
define('PRIX_UNITAIRE', 5.00); // 5 F CFA
define('PRIX_ABONNEMENT', 500.00); // 500 F CFA

// Configuration de sécurité
define('SESSION_LIFETIME', 3600); // 1 heure
define('DOWNLOAD_TOKEN_LIFETIME', 300); // 5 minutes

// Connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction pour nettoyer les entrées utilisateur
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Fonction pour générer un token sécurisé
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Fonction pour rediriger
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fonction pour formater le prix
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' F CFA';
}

// Fonction pour formater la durée
function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf("%02d:%02d", $minutes, $seconds);
} 
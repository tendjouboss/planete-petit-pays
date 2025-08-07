<?php
/**
 * Fonctions utilitaires
 * Plateforme Musicale - Planète Petit Pays
 */

require_once 'config.php';

/**
 * Initialise la session
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Vérifie si l'utilisateur a un abonnement actif
 */
function hasActiveSubscription($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT abonnement_actif, date_abonnement FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) return false;
    
    if (!$user['abonnement_actif']) return false;
    
    // Vérifier si l'abonnement n'a pas expiré (30 jours)
    $dateAbonnement = new DateTime($user['date_abonnement']);
    $dateExpiration = $dateAbonnement->modify('+30 days');
    $dateActuelle = new DateTime();
    
    return $dateActuelle <= $dateExpiration;
}

/**
 * Vérifie si l'utilisateur a déjà acheté un fichier
 */
function hasPurchasedFile($userId, $fileId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM achats WHERE user_id = ? AND fichier_id = ?");
    $stmt->execute([$userId, $fileId]);
    return $stmt->fetch() !== false;
}

/**
 * Crée un token de téléchargement sécurisé
 */
function createDownloadToken($userId, $fileId) {
    $pdo = getDBConnection();
    $token = generateToken();
    $expiration = date('Y-m-d H:i:s', time() + DOWNLOAD_TOKEN_LIFETIME);
    
    $stmt = $pdo->prepare("INSERT INTO sessions_telechargement (user_id, fichier_id, token, date_expiration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $fileId, $token, $expiration]);
    
    return $token;
}

/**
 * Valide un token de téléchargement
 */
function validateDownloadToken($token) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT st.*, f.url, f.titre 
        FROM sessions_telechargement st 
        JOIN fichiers f ON st.fichier_id = f.id 
        WHERE st.token = ? AND st.date_expiration > NOW() AND st.utilise = 0
    ");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

/**
 * Marque un token comme utilisé
 */
function markTokenAsUsed($token) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE sessions_telechargement SET utilise = 1 WHERE token = ?");
    $stmt->execute([$token]);
}

/**
 * Enregistre un achat
 */
function recordPurchase($userId, $fileId, $transactionId = null) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO achats (user_id, fichier_id, transaction_id) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $fileId, $transactionId]);
}

/**
 * Enregistre une transaction
 */
function recordTransaction($userId, $typePaiement, $montant) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type_paiement, montant) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $typePaiement, $montant]);
    return $pdo->lastInsertId();
}

/**
 * Met à jour le statut d'abonnement
 */
function updateSubscriptionStatus($userId, $active) {
    $pdo = getDBConnection();
    $dateAbonnement = $active ? date('Y-m-d H:i:s') : null;
    $stmt = $pdo->prepare("UPDATE users SET abonnement_actif = ?, date_abonnement = ? WHERE id = ?");
    $stmt->execute([$active ? 1 : 0, $dateAbonnement, $userId]);
}

/**
 * Récupère les albums avec leurs fichiers
 */
function getAlbumsWithFiles() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT a.*, COUNT(f.id) as nb_fichiers
        FROM albums a
        LEFT JOIN fichiers f ON a.id = f.album_id AND f.actif = 1
        WHERE a.actif = 1
        GROUP BY a.id
        ORDER BY a.date_sortie DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Récupère les fichiers d'un album
 */
function getAlbumFiles($albumId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM fichiers 
        WHERE album_id = ? AND actif = 1 
        ORDER BY titre
    ");
    $stmt->execute([$albumId]);
    return $stmt->fetchAll();
}

/**
 * Récupère l'historique des achats d'un utilisateur
 */
function getUserPurchaseHistory($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT a.*, f.titre, f.type, f.prix, alb.titre as album_titre
        FROM achats a
        JOIN fichiers f ON a.fichier_id = f.id
        LEFT JOIN albums alb ON f.album_id = alb.id
        WHERE a.user_id = ?
        ORDER BY a.date_achat DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Nettoie les anciens tokens expirés
 */
function cleanupExpiredTokens() {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM sessions_telechargement WHERE date_expiration < NOW()");
    $stmt->execute();
}

/**
 * Simule un paiement Mobile Money
 */
function simulateMobileMoneyPayment($amount, $phoneNumber) {
    // Simulation - en production, intégrer une vraie API
    $success = rand(0, 10) > 2; // 80% de succès
    
    if ($success) {
        return [
            'success' => true,
            'reference' => 'PAY' . time() . rand(1000, 9999),
            'message' => 'Paiement effectué avec succès'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Échec du paiement'
        ];
    }
}

/**
 * Formate la taille de fichier
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Génère un message d'erreur/succès
 */
function setMessage($type, $message) {
    $_SESSION['message'] = [
        'type' => $type,
        'text' => $message
    ];
}

/**
 * Affiche et supprime le message
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        
        $bgColor = $message['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        
        return "<div class='$bgColor border px-4 py-3 rounded relative mb-4' role='alert'>
                    <span class='block sm:inline'>" . htmlspecialchars($message['text']) . "</span>
                </div>";
    }
    return '';
} 

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdminOrRedirect() {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('index.php');
        exit();
    }
} 
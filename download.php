<?php
require_once 'includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    setMessage('error', 'Vous devez être connecté pour télécharger');
    redirect('login.php');
}

// Récupérer le token
$token = $_GET['token'] ?? '';

if (empty($token)) {
    setMessage('error', 'Token de téléchargement invalide');
    redirect('index.php');
}

// Valider le token
$downloadInfo = validateDownloadToken($token);

if (!$downloadInfo) {
    setMessage('error', 'Token de téléchargement expiré ou invalide');
    redirect('index.php');
}

// Vérifier que l'utilisateur a le droit de télécharger ce fichier
if ($downloadInfo['user_id'] != $_SESSION['user_id']) {
    setMessage('error', 'Accès non autorisé');
    redirect('index.php');
}

// Marquer le token comme utilisé
markTokenAsUsed($token);

// Chemin du fichier
$filePath = UPLOAD_PATH . $downloadInfo['url'];

if (!file_exists($filePath)) {
    setMessage('error', 'Fichier non trouvé');
    redirect('index.php');
}

// Déterminer le type MIME
$fileExtension = pathinfo($downloadInfo['url'], PATHINFO_EXTENSION);
$mimeTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime'
];

$mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

// Enregistrer le téléchargement dans l'historique
recordPurchase($_SESSION['user_id'], $downloadInfo['fichier_id']);

// Nettoyer les anciens tokens expirés
cleanupExpiredTokens();

// Configuration des headers pour le téléchargement
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . basename($downloadInfo['url']) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Lire et envoyer le fichier
readfile($filePath);
exit;
?> 
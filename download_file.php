<?php
require_once 'includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(403);
    die('Accès refusé');
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    die('Token manquant');
}

// Valider le token
$tokenData = validateDownloadToken($token);

if (!$tokenData) {
    http_response_code(403);
    die('Token invalide ou expiré');
}

// Vérifier que l'utilisateur correspond
if ($tokenData['user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    die('Accès non autorisé');
}

// Vérifier que le fichier existe
$filePath = $tokenData['url'];
if (!file_exists($filePath)) {
    http_response_code(404);
    die('Fichier non trouvé');
}

// Marquer le token comme utilisé
markTokenAsUsed($token);

// Configuration du téléchargement
$fileName = $tokenData['titre'];
$fileSize = filesize($filePath);
$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

// Définir le type MIME approprié
$mimeTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'm4a' => 'audio/mp4',
    'mp4' => 'video/mp4',
    'avi' => 'video/x-msvideo',
    'mov' => 'video/quicktime',
    'wmv' => 'video/x-ms-wmv'
];

$mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

// En-têtes HTTP pour le téléchargement
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $fileName . '.' . $fileExtension . '"');
header('Content-Length: ' . $fileSize);
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Nettoyer le buffer de sortie
if (ob_get_level()) {
    ob_end_clean();
}

// Lire et envoyer le fichier
$handle = fopen($filePath, 'rb');
if ($handle) {
    while (!feof($handle)) {
        echo fread($handle, 8192);
        flush();
    }
    fclose($handle);
} else {
    http_response_code(500);
    die('Erreur lors de la lecture du fichier');
}

// Enregistrer le téléchargement dans les logs (optionnel)
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO download_logs (user_id, fichier_id, token, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $tokenData['fichier_id'],
        $token,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);
} catch (Exception $e) {
    // Log silencieux en cas d'erreur
    error_log('Erreur lors de l\'enregistrement du log de téléchargement: ' . $e->getMessage());
}

exit;
?> 
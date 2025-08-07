<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);
$fileId = (int)($input['file_id'] ?? 0);

if (!$fileId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de fichier invalide']);
    exit;
}

try {
    // Vérifier que l'utilisateur peut télécharger ce fichier
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE id = ? AND actif = 1");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch();
    
    if (!$file) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Fichier non trouvé']);
        exit;
    }
    
    // Vérifier les droits de téléchargement
    $hasSubscription = hasActiveSubscription($_SESSION['user_id']);
    $hasPurchased = hasPurchasedFile($_SESSION['user_id'], $fileId);
    
    if (!$hasSubscription && !$hasPurchased) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Achat requis pour télécharger ce fichier']);
        exit;
    }
    
    // Créer le token de téléchargement
    $token = createDownloadToken($_SESSION['user_id'], $fileId);
    
    echo json_encode([
        'success' => true,
        'token' => $token,
        'message' => 'Token créé avec succès'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?> 
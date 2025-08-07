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

// Récupérer l'ID de l'album
$albumId = (int)($_GET['id'] ?? 0);

if (!$albumId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID d\'album invalide']);
    exit;
}

try {
    // Récupérer les informations de l'album
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND actif = 1");
    $stmt->execute([$albumId]);
    $album = $stmt->fetch();
    
    if (!$album) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Album non trouvé']);
        exit;
    }
    
    // Récupérer les fichiers de l'album
    $files = getAlbumFiles($albumId);
    
    // Vérifier le statut d'abonnement
    $hasSubscription = hasActiveSubscription($_SESSION['user_id']);
    
    // Générer le HTML pour les fichiers
    $html = '<div class="space-y-4">';
    
    if (!empty($files)) {
        foreach ($files as $file) {
            $hasPurchased = hasPurchasedFile($_SESSION['user_id'], $file['id']);
            $canDownload = $hasSubscription || $hasPurchased;
            
            $html .= '<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">';
            $html .= '<div class="flex items-center space-x-4">';
            $html .= '<div class="w-10 h-10 bg-gradient-to-br from-primary-red to-primary-blue rounded-lg flex items-center justify-center">';
            $html .= '<i class="fas fa-' . ($file['type'] === 'audio' ? 'music' : 'video') . ' text-white"></i>';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<h4 class="font-semibold text-gray-900">' . htmlspecialchars($file['titre']) . '</h4>';
            $html .= '<div class="flex items-center space-x-4 text-sm text-gray-500">';
            $html .= '<span><i class="fas fa-' . ($file['type'] === 'audio' ? 'music' : 'video') . ' mr-1"></i>' . ucfirst($file['type']) . '</span>';
            if ($file['duree']) {
                $html .= '<span><i class="fas fa-clock mr-1"></i>' . $file['duree'] . '</span>';
            }
            if ($file['taille_fichier']) {
                $html .= '<span><i class="fas fa-file mr-1"></i>' . formatFileSize($file['taille_fichier']) . '</span>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="flex items-center space-x-2">';
            
            if ($canDownload) {
                $html .= '<button onclick="downloadFile(' . $file['id'] . ')" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">';
                $html .= '<i class="fas fa-download mr-1"></i>Télécharger';
                $html .= '</button>';
            } else {
                $html .= '<button onclick="confirmPayment(\'unique\', ' . $file['prix'] . ', ' . $file['id'] . ')" class="bg-primary-red text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">';
                $html .= '<i class="fas fa-shopping-cart mr-1"></i>' . formatPrice($file['prix']);
                $html .= '</button>';
            }
            
            $html .= '<button onclick="previewMedia(\'' . htmlspecialchars($file['url']) . '\', \'' . $file['type'] . '\')" class="border border-primary-red text-primary-red px-3 py-1 rounded text-sm hover:bg-primary-red hover:text-white transition-colors">';
            $html .= '<i class="fas fa-play"></i>';
            $html .= '</button>';
            $html .= '</div>';
            $html .= '</div>';
        }
    } else {
        $html .= '<div class="text-center py-8">';
        $html .= '<i class="fas fa-music text-4xl text-gray-300 mb-4"></i>';
        $html .= '<p class="text-gray-500">Aucun morceau disponible dans cet album.</p>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    echo json_encode([
        'success' => true,
        'album' => $album,
        'files' => $files,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
}
?> 
<?php
/**
 * Script de test pour la page profil
 * Ajoute des données d'exemple pour tester
 */

require_once 'includes/functions.php';
initSession();

// Se connecter en tant qu'admin pour les tests
$_SESSION['user_id'] = 1; // ID de l'admin
$_SESSION['user_role'] = 'admin';

echo "<h1>Test de la page profil</h1>";

try {
    $pdo = getDBConnection();
    
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([1]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<p style='color: red;'>Erreur: Utilisateur admin non trouvé</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Utilisateur trouvé: " . htmlspecialchars($user['nom']) . "</p>";
    
    // Tester la fonction hasActiveSubscription
    $hasSubscription = hasActiveSubscription(1);
    echo "<p>Abonnement actif: " . ($hasSubscription ? 'Oui' : 'Non') . "</p>";
    
    // Tester la fonction getUserPurchaseHistory
    $purchaseHistory = getUserPurchaseHistory(1);
    echo "<p>Nombre d'achats: " . count($purchaseHistory) . "</p>";
    
    // Ajouter quelques achats de test si aucun n'existe
    if (empty($purchaseHistory)) {
        echo "<p>Ajout d'achats de test...</p>";
        
        // Récupérer quelques fichiers
        $stmt = $pdo->prepare("SELECT id FROM fichiers LIMIT 3");
        $stmt->execute();
        $files = $stmt->fetchAll();
        
        foreach ($files as $file) {
            $stmt = $pdo->prepare("INSERT INTO achats (user_id, fichier_id) VALUES (?, ?)");
            $stmt->execute([1, $file['id']]);
            echo "<p>✅ Achat ajouté pour le fichier ID: " . $file['id'] . "</p>";
        }
        
        // Recharger l'historique
        $purchaseHistory = getUserPurchaseHistory(1);
        echo "<p>Nouveau nombre d'achats: " . count($purchaseHistory) . "</p>";
    }
    
    // Tester la création d'un token de téléchargement
    if (!empty($files)) {
        $token = createDownloadToken(1, $files[0]['id']);
        echo "<p>✅ Token créé: " . substr($token, 0, 10) . "...</p>";
        
        // Tester la validation du token
        $downloadInfo = validateDownloadToken($token);
        if ($downloadInfo) {
            echo "<p>✅ Token validé avec succès</p>";
        } else {
            echo "<p style='color: red;'>❌ Erreur de validation du token</p>";
        }
    }
    
    echo "<h2>Test de la page profil</h2>";
    echo "<p><a href='profile.php' target='_blank'>Ouvrir la page profil</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?> 
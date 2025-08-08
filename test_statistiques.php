<?php
/**
 * Script de test pour les statistiques d'administration
 * Ajoute des données d'exemple pour tester
 */

require_once 'includes/functions.php';
initSession();

// Se connecter en tant qu'admin pour les tests
$_SESSION['user_id'] = 1; // ID de l'admin
$_SESSION['user_role'] = 'admin';

echo "<h1>Test des Statistiques d'Administration</h1>";

try {
    $pdo = getDBConnection();
    
    // Tester les fonctions de statistiques
    echo "<h2>1. Statistiques générales</h2>";
    $stats = getAdminStatistics($pdo);
    echo "<ul>";
    echo "<li>Total téléchargements : " . $stats['total_downloads'] . "</li>";
    echo "<li>Total utilisateurs : " . $stats['total_users'] . "</li>";
    echo "<li>Total albums : " . $stats['total_albums'] . "</li>";
    echo "<li>Total fichiers : " . $stats['total_files'] . "</li>";
    echo "</ul>";
    
    echo "<h2>2. Statistiques par album</h2>";
    $statsAlbums = getAlbumStatistics($pdo);
    echo "<ul>";
    foreach ($statsAlbums as $album) {
        echo "<li>" . htmlspecialchars($album['titre']) . " - " . $album['total_downloads'] . " téléchargements</li>";
    }
    echo "</ul>";
    
    echo "<h2>3. Statistiques par fichier (Top 10)</h2>";
    $statsFichiers = getFileStatistics($pdo);
    echo "<ul>";
    foreach ($statsFichiers as $fichier) {
        echo "<li>" . htmlspecialchars($fichier['titre']) . " - " . $fichier['downloads'] . " téléchargements</li>";
    }
    echo "</ul>";
    
    echo "<h2>4. Statistiques des utilisateurs</h2>";
    $statsUsers = getUserStatistics($pdo);
    echo "<ul>";
    echo "<li>Total utilisateurs : " . $statsUsers['total_users'] . "</li>";
    echo "<li>Abonnements actifs : " . $statsUsers['active_subscriptions'] . "</li>";
    echo "<li>Moyenne téléchargements/utilisateur : " . number_format($statsUsers['avg_downloads_per_user'], 1) . "</li>";
    echo "</ul>";
    
    // Ajouter des achats de test si aucun n'existe
    if ($stats['total_downloads'] == 0) {
        echo "<h2>Ajout d'achats de test...</h2>";
        
        // Récupérer quelques fichiers
        $stmt = $pdo->prepare("SELECT id FROM fichiers LIMIT 5");
        $stmt->execute();
        $files = $stmt->fetchAll();
        
        // Ajouter des achats pour différents utilisateurs
        $users = [1, 2, 3]; // IDs d'utilisateurs de test
        
        foreach ($files as $file) {
            foreach ($users as $userId) {
                // Vérifier si l'achat n'existe pas déjà
                $stmt = $pdo->prepare("SELECT id FROM achats WHERE user_id = ? AND fichier_id = ?");
                $stmt->execute([$userId, $file['id']]);
                if (!$stmt->fetch()) {
                    $stmt = $pdo->prepare("INSERT INTO achats (user_id, fichier_id) VALUES (?, ?)");
                    $stmt->execute([$userId, $file['id']]);
                    echo "<p>✅ Achat ajouté - Utilisateur $userId, Fichier " . $file['id'] . "</p>";
                }
            }
        }
        
        echo "<h3>Nouvelles statistiques après ajout :</h3>";
        $newStats = getAdminStatistics($pdo);
        echo "<p>Total téléchargements : " . $newStats['total_downloads'] . "</p>";
    }
    
    echo "<h2>Test de la page statistiques</h2>";
    echo "<p><a href='admin/statistiques.php' target='_blank'>Ouvrir la page statistiques</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?> 
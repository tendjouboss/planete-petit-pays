<?php
/**
 * Script d'initialisation pour Railway
 * Crée les dossiers nécessaires et vérifie la configuration
 */

echo "🚀 Initialisation de Planète Petit Pays...\n";

// Créer les dossiers uploads s'ils n'existent pas
$directories = [
    'assets/uploads',
    'assets/uploads/audio',
    'assets/uploads/video',
    'assets/uploads/albums'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Dossier créé : $dir\n";
        } else {
            echo "❌ Erreur création dossier : $dir\n";
        }
    } else {
        echo "✅ Dossier existe déjà : $dir\n";
    }
}

// Vérifier la configuration de la base de données
echo "\n🔍 Vérification de la configuration...\n";

// Charger la configuration
require_once 'includes/config.php';

try {
    $pdo = getDBConnection();
    echo "✅ Connexion à la base de données réussie\n";
    
    // Vérifier si les tables existent
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "⚠️  Aucune table trouvée. Veuillez importer le schéma de base de données.\n";
        echo "📋 Utilisez le fichier database/schema.sql\n";
    } else {
        echo "✅ Tables trouvées : " . implode(', ', $tables) . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    echo "🔧 Vérifiez vos variables d'environnement :\n";
    echo "   - DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'non défini') . "\n";
    echo "   - DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'non défini') . "\n";
    echo "   - DB_USER: " . (defined('DB_USER') ? DB_USER : 'non défini') . "\n";
}

// Vérifier les variables d'environnement
echo "\n🔧 Variables d'environnement :\n";
echo "   - SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'non défini') . "\n";
echo "   - UPLOAD_PATH: " . (defined('UPLOAD_PATH') ? UPLOAD_PATH : 'non défini') . "\n";

// Vérifier les permissions des dossiers
echo "\n📁 Permissions des dossiers :\n";
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "   - $dir : $perms\n";
    }
}

echo "\n🎉 Initialisation terminée !\n";
echo "🌐 Votre application devrait être accessible sur : " . (defined('SITE_URL') ? SITE_URL : 'URL non définie') . "\n";
?> 
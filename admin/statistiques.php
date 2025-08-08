<?php
require_once '../includes/functions.php';
initSession();
isAdminOrRedirect();
$pageTitle = 'Statistiques - Administration';
require_once '../includes/admin_header.php';

// Récupérer les statistiques
try {
    $pdo = getDBConnection();
    
    // Statistiques générales
    $stats = getAdminStatistics($pdo);
    
    // Statistiques par album
    $statsAlbums = getAlbumStatistics($pdo);
    
    // Statistiques par fichier
    $statsFichiers = getFileStatistics($pdo);
    
    // Statistiques des utilisateurs
    $statsUsers = getUserStatistics($pdo);
    
} catch (Exception $e) {
    $error = "Erreur lors du chargement des statistiques : " . $e->getMessage();
}
?>

<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8 text-center text-primary-red">
        <i class="fas fa-chart-bar mr-3"></i>Statistiques de la Plateforme
    </h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- Statistiques générales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-primary-red">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-primary-red">
                    <i class="fas fa-download text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Téléchargements</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_downloads']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-primary-blue">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-primary-blue">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Utilisateurs Inscrits</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-primary-yellow">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-primary-yellow">
                    <i class="fas fa-compact-disc text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Albums</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_albums']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-primary-orange">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-primary-orange">
                    <i class="fas fa-music text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Fichiers</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_files']); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiques par album -->
    <div class="bg-white rounded-lg shadow-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-compact-disc mr-2 text-primary-red"></i>
                Statistiques par Album
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Album</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fichiers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléchargements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moyenne</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($statsAlbums as $album): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($album['titre']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($album['description']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo $album['nb_fichiers']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo number_format($album['total_downloads']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary-red">
                            <?php echo formatPrice($album['total_revenue']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo $album['nb_fichiers'] > 0 ? number_format($album['total_downloads'] / $album['nb_fichiers'], 1) : '0'; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Statistiques par fichier -->
    <div class="bg-white rounded-lg shadow-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-music mr-2 text-primary-blue"></i>
                Top 10 des Fichiers les Plus Téléchargés
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fichier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Album</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléchargements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($statsFichiers as $fichier): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($fichier['titre']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo $fichier['duree']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($fichier['album_titre']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $fichier['type'] === 'audio' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'; ?>">
                                <i class="fas fa-<?php echo $fichier['type'] === 'audio' ? 'music' : 'video'; ?> mr-1"></i>
                                <?php echo ucfirst($fichier['type']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo number_format($fichier['downloads']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-primary-red">
                            <?php echo formatPrice($fichier['revenue']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo formatPrice($fichier['prix']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Statistiques des utilisateurs -->
    <div class="bg-white rounded-lg shadow-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-users mr-2 text-primary-yellow"></i>
                Statistiques des Utilisateurs
            </h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-red"><?php echo number_format($statsUsers['total_users']); ?></div>
                    <div class="text-sm text-gray-600">Utilisateurs inscrits</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-blue"><?php echo number_format($statsUsers['active_subscriptions']); ?></div>
                    <div class="text-sm text-gray-600">Abonnements actifs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-green"><?php echo number_format($statsUsers['avg_downloads_per_user'], 1); ?></div>
                    <div class="text-sm text-gray-600">Moyenne téléchargements/utilisateur</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="flex justify-center space-x-4">
        <a href="index.php" class="inline-flex items-center px-6 py-3 bg-primary-red text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour à l'administration
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-6 py-3 bg-primary-blue text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>Imprimer les statistiques
        </button>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?> 
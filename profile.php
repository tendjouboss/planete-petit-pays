<?php
require_once 'includes/functions.php';
initSession();

if (!isLoggedIn()) {
    redirect('login.php');
}

$pageTitle = "Mon Profil";

// Récupérer les informations de l'utilisateur
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Récupérer l'historique des achats
$purchaseHistory = getUserPurchaseHistory($_SESSION['user_id']);

// Vérifier le statut d'abonnement
$hasActiveSubscription = hasActiveSubscription($_SESSION['user_id']);
?>

<div class="max-w-7xl mx-auto">
    <!-- Header du profil -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-primary-red to-primary-orange p-6 text-white">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($user['nom']); ?></h1>
                    <p class="text-red-100"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Informations personnelles -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nom :</span>
                            <span class="font-medium"><?php echo htmlspecialchars($user['nom']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email :</span>
                            <span class="font-medium"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Membre depuis :</span>
                            <span class="font-medium"><?php echo date('d/m/Y', strtotime($user['date_creation'])); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Statut d'abonnement -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Abonnement</h3>
                    <?php if ($hasActiveSubscription): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-crown text-green-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-semibold text-green-800">Abonnement actif</div>
                                    <div class="text-sm text-green-600">Accès illimité</div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-600 text-xl mr-3"></i>
                                <div>
                                    <div class="font-semibold text-gray-800">Compte gratuit</div>
                                    <div class="text-sm text-gray-600">Achat à l'unité</div>
                                </div>
                            </div>
                        </div>
                        <a href="payment.php?type=abo&amount=<?php echo PRIX_ABONNEMENT; ?>" 
                           class="mt-3 inline-flex items-center px-4 py-2 bg-primary-red text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-crown mr-2"></i>S'abonner
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Statistiques -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Téléchargements :</span>
                            <span class="font-semibold text-primary-red"><?php echo count($purchaseHistory); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Dernier achat :</span>
                            <span class="font-medium">
                                <?php 
                                if (!empty($purchaseHistory)) {
                                    echo date('d/m/Y', strtotime($purchaseHistory[0]['date_achat']));
                                } else {
                                    echo 'Aucun';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Historique des achats -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Historique des téléchargements</h2>
        </div>
        
        <?php if (!empty($purchaseHistory)): ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($purchaseHistory as $purchase): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary-red to-primary-blue rounded-lg flex items-center justify-center">
                                    <i class="fas fa-<?php echo $purchase['type'] === 'audio' ? 'music' : 'video'; ?> text-white"></i>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($purchase['titre']); ?>
                                    </h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-<?php echo $purchase['type'] === 'audio' ? 'music' : 'video'; ?> mr-1"></i>
                                            <?php echo ucfirst($purchase['type']); ?>
                                        </span>
                                        <?php if ($purchase['album_titre']): ?>
                                            <span>
                                                <i class="fas fa-compact-disc mr-1"></i>
                                                <?php echo htmlspecialchars($purchase['album_titre']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($purchase['date_achat'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-primary-red">
                                    <?php echo formatPrice($purchase['prix']); ?>
                                </span>
                                <button onclick="downloadFile(<?php echo $purchase['fichier_id']; ?>)" 
                                        class="bg-primary-red text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>Télécharger
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center">
                <i class="fas fa-download text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun téléchargement</h3>
                <p class="text-gray-500 mb-6">Vous n'avez pas encore téléchargé de fichiers.</p>
                <a href="albums.php" class="inline-flex items-center px-6 py-3 bg-primary-red text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-compact-disc mr-2"></i>Découvrir les albums
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Actions -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
        <a href="albums.php" class="inline-flex items-center px-6 py-3 border border-primary-red text-primary-red rounded-lg hover:bg-primary-red hover:text-white transition-colors">
            <i class="fas fa-compact-disc mr-2"></i>Voir les albums
        </a>
        <a href="index.php" class="inline-flex items-center px-6 py-3 border border-primary-orange text-primary-orange rounded-lg hover:bg-primary-orange hover:text-white transition-colors">
            <i class="fas fa-home mr-2"></i>Retour à l'accueil
        </a>
    </div>
</div>

<script>
function downloadFile(fileId) {
    // Créer un token de téléchargement
    fetch('ajax/create_download_token.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            file_id: fileId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Rediriger vers le téléchargement
            window.location.href = `download.php?token=${data.token}`;
        } else {
            alert('Erreur lors de la création du lien de téléchargement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du téléchargement');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?> 
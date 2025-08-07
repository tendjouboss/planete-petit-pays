<?php
require_once 'includes/functions.php';
initSession();

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    setMessage('error', 'Vous devez être connecté pour effectuer un paiement');
    redirect('login.php');
}

$album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
$file_id = isset($_GET['file_id']) ? (int)$_GET['file_id'] : 0;
$type = $_GET['type'] ?? 'download';
$amount = isset($_GET['amount']) ? (float)$_GET['amount'] : 0;

// Vérifier qu'on a soit un album, soit un fichier, soit un abonnement
if (!$album_id && !$file_id && $type !== 'subscription') {
    setMessage('error', 'Aucun contenu sélectionné');
    redirect('albums.php');
}

try {
    $pdo = getDBConnection();
    
    if ($album_id) {
        // Paiement pour un album complet
        $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND actif = 1");
        $stmt->execute([$album_id]);
        $album = $stmt->fetch();
        
        if (!$album) {
            setMessage('error', 'Album non trouvé ou non disponible');
            redirect('albums.php');
        }
        
        // Récupérer les fichiers de l'album
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE album_id = ? AND actif = 1 ORDER BY titre");
        $stmt->execute([$album_id]);
        $fichiers = $stmt->fetchAll();
        
        if (empty($fichiers)) {
            setMessage('error', 'Aucun fichier disponible pour cet album');
            redirect('albums.php');
        }
        
        // Calculer le coût total
        $total_cost = 0;
        foreach ($fichiers as $fichier) {
            $total_cost += $fichier['prix'];
        }
        
        $pageTitle = 'Paiement - ' . $album['titre'];
        
    } elseif ($type === 'subscription') {
        // Paiement pour un abonnement
        $total_cost = $amount;
        $pageTitle = 'Paiement - Abonnement';
        
    } else {
        // Paiement pour un fichier individuel
        $stmt = $pdo->prepare("SELECT f.*, a.titre as album_titre, a.image_url as album_image FROM fichiers f 
                              JOIN albums a ON f.album_id = a.id 
                              WHERE f.id = ? AND f.actif = 1 AND a.actif = 1");
        $stmt->execute([$file_id]);
        $fichier = $stmt->fetch();
        
        if (!$fichier) {
            setMessage('error', 'Fichier non trouvé ou non disponible');
            redirect('albums.php');
        }
        
        $fichiers = [$fichier];
        $total_cost = $fichier['prix'];
        
        $pageTitle = 'Paiement - ' . $fichier['titre'];
    }
    
} catch (PDOException $e) {
    setMessage('error', 'Erreur lors de la récupération des données');
    redirect('albums.php');
}

// Définir le titre de la page selon le type de paiement
if ($type === 'subscription') {
    $pageTitle = 'Paiement - Abonnement';
} elseif ($album_id) {
    $pageTitle = 'Paiement - ' . $album['titre'];
} else {
    $pageTitle = 'Paiement - ' . $fichiers[0]['titre'];
}

require_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-6 bg-gradient-to-r from-primary-red to-primary-orange bg-clip-text text-transparent">
            Paiement
        </h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            <?php if ($type === 'subscription'): ?>
                Finalisez votre abonnement pour accéder à tous les téléchargements
            <?php elseif ($album_id): ?>
                Finalisez votre achat pour télécharger "<?= htmlspecialchars($album['titre']) ?>"
            <?php else: ?>
                Finalisez votre achat pour télécharger "<?= htmlspecialchars($fichiers[0]['titre']) ?>"
            <?php endif; ?>
        </p>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Résumé de la commande -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-shopping-cart mr-3 text-primary-red"></i>
                Résumé de la commande
            </h2>
            
            <!-- Informations de l'album/fichier/abonnement -->
            <div class="flex items-center space-x-4 mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="w-16 h-16 bg-gradient-to-br from-primary-red to-primary-blue rounded-lg overflow-hidden flex-shrink-0">
                    <?php if ($type === 'subscription'): ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-crown text-2xl text-white opacity-80"></i>
                        </div>
                    <?php elseif ($album_id): ?>
                        <?php if (!empty($album['image_url'])): ?>
                            <img src="<?= htmlspecialchars($album['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($album['titre']) ?>" 
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-compact-disc text-2xl text-white opacity-80"></i>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (!empty($fichiers[0]['album_image'])): ?>
                            <img src="<?= htmlspecialchars($fichiers[0]['album_image']) ?>" 
                                 alt="<?= htmlspecialchars($fichiers[0]['album_titre']) ?>" 
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-<?= $fichiers[0]['type'] === 'audio' ? 'music' : 'video' ?> text-2xl text-white opacity-80"></i>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($type === 'subscription'): ?>
                        <h3 class="font-semibold text-gray-900">Abonnement Premium</h3>
                        <p class="text-sm text-gray-500">Accès illimité à tous les téléchargements</p>
                    <?php elseif ($album_id): ?>
                        <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($album['titre']) ?></h3>
                        <p class="text-sm text-gray-500"><?= count($fichiers) ?> morceaux</p>
                    <?php else: ?>
                        <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($fichiers[0]['titre']) ?></h3>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($fichiers[0]['album_titre']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Liste des fichiers ou avantages abonnement -->
            <div class="space-y-3 mb-6">
                <?php if ($type === 'subscription'): ?>
                    <h4 class="font-semibold text-gray-900">Avantages de l'abonnement :</h4>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-download text-green-600"></i>
                            <span class="text-sm text-gray-700">Téléchargements illimités</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-music text-green-600"></i>
                            <span class="text-sm text-gray-700">Accès à tous les albums</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-crown text-green-600"></i>
                            <span class="text-sm text-gray-700">Priorité sur les nouveaux contenus</span>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-calendar text-green-600"></i>
                            <span class="text-sm text-gray-700">Durée : 30 jours</span>
                        </div>
                    </div>
                <?php else: ?>
                    <h4 class="font-semibold text-gray-900">
                        <?php if ($album_id): ?>
                            Fichiers inclus :
                        <?php else: ?>
                            Fichier à télécharger :
                        <?php endif; ?>
                    </h4>
                    <?php foreach ($fichiers as $fichier): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-<?= $fichier['type'] === 'audio' ? 'music' : 'video' ?> text-primary-blue"></i>
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($fichier['titre']) ?></span>
                            </div>
                            <span class="text-sm font-semibold text-primary-red"><?= $fichier['prix'] ?> F CFA</span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Total -->
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center justify-between text-lg font-bold text-gray-900">
                    <span>Total :</span>
                    <span class="text-primary-red"><?= $total_cost ?> F CFA</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Prix par morceau : 5 F CFA
                </p>
            </div>
        </div>

        <!-- Formulaire de paiement -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card mr-3 text-primary-blue"></i>
                Informations de paiement
            </h2>
            
            <form method="POST" action="process_payment.php" class="space-y-6">
                <?php if ($album_id): ?>
                    <input type="hidden" name="album_id" value="<?= $album_id ?>">
                <?php elseif ($type === 'subscription'): ?>
                    <input type="hidden" name="subscription" value="1">
                <?php else: ?>
                    <input type="hidden" name="file_id" value="<?= $file_id ?>">
                <?php endif; ?>
                <input type="hidden" name="total_amount" value="<?= $total_cost ?>">
                <input type="hidden" name="type" value="<?= $type ?>">
                
                <!-- Informations personnelles -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nom complet</label>
                    <input type="text" name="full_name" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-primary-blue focus:ring-2 focus:ring-primary-blue/20 transition-all"
                           value="<?= htmlspecialchars($_SESSION['user_nom'] ?? '') ?>">
                    </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-primary-blue focus:ring-2 focus:ring-primary-blue/20 transition-all"
                           value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Téléphone</label>
                    <input type="tel" name="phone" required
                           class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 focus:border-primary-blue focus:ring-2 focus:ring-primary-blue/20 transition-all"
                           placeholder="+225 0123456789">
                </div>
                
                                 <!-- Méthode de paiement -->
                 <div>
                     <label class="block text-gray-700 font-semibold mb-3">Méthode de paiement</label>
                     <div class="space-y-3">
                         <label class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:border-primary-blue cursor-pointer">
                             <input type="radio" name="payment_method" value="orange_money" checked
                                    class="text-primary-blue focus:ring-primary-blue">
                             <div>
                                 <div class="font-semibold text-gray-900">Orange Money</div>
                                 <div class="text-sm text-gray-500">Paiement sécurisé via Orange Money</div>
                             </div>
                         </label>
                         
                         <label class="flex items-center space-x-3 p-3 border-2 border-gray-200 rounded-lg hover:border-primary-blue cursor-pointer">
                             <input type="radio" name="payment_method" value="mtn_money"
                                    class="text-primary-blue focus:ring-primary-blue">
                             <div>
                                 <div class="font-semibold text-gray-900">MTN Mobile Money</div>
                                 <div class="text-sm text-gray-500">Paiement sécurisé via MTN Mobile Money</div>
                             </div>
                         </label>
                     </div>
                 </div>
                
                <!-- Conditions -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="flex items-start space-x-3">
                        <input type="checkbox" name="terms" required
                               class="mt-1 text-primary-blue focus:ring-primary-blue">
                        <div class="text-sm text-gray-600">
                            J'accepte les <a href="#" class="text-primary-blue hover:underline">conditions d'utilisation</a> 
                            et la <a href="#" class="text-primary-blue hover:underline">politique de confidentialité</a>
                        </div>
                    </label>
                </div>
                
                                 <!-- Bouton de paiement -->
                 <button type="submit" 
                         class="w-full bg-gradient-to-r from-primary-red to-primary-orange text-white py-4 rounded-lg font-semibold text-lg hover:from-red-700 hover:to-orange-700 transition-all transform hover:scale-105">
                     <i class="fas fa-mobile-alt mr-2"></i>
                     Payer <?= $total_cost ?> F CFA par Mobile Money
                 </button>
            </form>
            
            <!-- Alternative abonnement -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-crown text-blue-500 text-xl"></i>
                        <div>
                        <h4 class="font-semibold text-blue-900">Économisez avec un abonnement !</h4>
                        <p class="text-sm text-blue-700">
                            Pour seulement 500 F CFA/mois, téléchargez tous les albums gratuitement
                        </p>
                        <a href="subscription.php" class="text-blue-600 hover:underline text-sm font-medium">
                            Voir les abonnements →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="album.php?id=<?= $album_id ?>" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Retour à l'album
        </a>
        <a href="albums.php" 
           class="bg-primary-blue text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-list mr-2"></i>Voir tous les albums
        </a>
    </div>
</div>

<script>
// Animation au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});
</script>

<?php require_once 'includes/footer.php'; ?> 